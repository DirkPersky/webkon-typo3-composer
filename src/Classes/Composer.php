<?php
declare(strict_types=1);
namespace DirkPersky\Typo3Composer\Classes;

class Composer {
    static $composer;

    public function __construct($loader) {
        $selfPath = $loader->findFile('DirkPersky\\Typo3Composer\\Classes\\Composer');
        preg_match("/^(.*?)vendor/", $selfPath, $matches);
        list($vendor, $base) = $matches;

        static::$composer = [
            'COMPOSER_HOME' => $vendor .'/bin',
            'COMPOSER' => $base,
            'OSTYPE' => 'OS400',
        ];
    }
    protected function getToken($hash){
        // create curl resource
        $ch = curl_init();
        // set url
        curl_setopt($ch, CURLOPT_URL, "https://webmanagement.gutenberghaus.de/token/auth/".urlencode($_SERVER['SERVER_NAME'])."?token=".urlencode($hash));
        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $output contains the output string
        $token = curl_exec($ch);
        // close curl resource to free up system resources
        curl_close($ch);

        $token = json_decode($token);
        return $token->access;
    }
    public function run(){
        try {
            if( empty($_SERVER['HTTP_X_AUTHORIZATION']) ) throw new \DirkPersky\Typo3Composer\Exception\Composer();
            header("Access-Control-Allow-Origin: *");
            if( static::getToken($_SERVER['HTTP_X_AUTHORIZATION'])) {
                $this->call($this->get('action', true));
            } else {
                throw new \DirkPersky\Typo3Composer\Exception\Composer();
            }

        } catch (\DirkPersky\Typo3Composer\Exception\Composer $ex) {
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . 404 . ' Page Not Found');
            require_once dirname(__DIR__).'/View/404.php';
            exit;
        }
    }
    protected function get($name, $throw = false){
        if(empty($_POST[$name])) {
            if($throw) throw new \DirkPersky\Typo3Composer\Exception\Composer();
            return '';
        }
        return $_POST[$name];
    }
    protected function call($command){
        $config = static::$composer;
        putenv("COMPOSER_HOME={$config['COMPOSER_HOME']}");
        putenv("COMPOSER={$config['COMPOSER']}composer.json" );
        putenv("OSTYPE={$config['OSTYPE']}"); //force to use php://output instead of php://stdout

        exec(trim(sprintf('cd %1$s && composer %2$s %3$s --ignore-platform-reqs', $config['COMPOSER'], $command, $this->get('options'))), $out,$return);
        die(json_encode($out));
    }
}
