<?php
declare(strict_types=1);
namespace DirkPersky\Typo3Composer\Classes;

use Composer\Console\Application;
use Composer\Script\Event as ScriptEvent;
use Symfony\Component\Console\Input\ArrayInput;

class Composer {
    static $composer;
    static $token;

    public function __construct($loader) {
        $selfPath = $loader->findFile('DirkPersky\\Typo3Composer\\Classes\\Composer');
        preg_match("/^(.*?)vendor/", $selfPath, $matches);
        list($vendor, $base) = $matches;

        static::$composer = [
            'COMPOSER_HOME' => $vendor .'/bin/composer',
            'COMPOSER' => $base,
            'OSTYPE' => 'OS400',
        ];
        static::getToken();
    }
    protected function getToken(){
        // create curl resource
        $ch = curl_init();
        // set url
        curl_setopt($ch, CURLOPT_URL, "https://webmanagement.gutenberghaus.de/token/auth/".urlencode($_SERVER['SERVER_NAME']));
        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // $output contains the output string
        static::$token = curl_exec($ch);
        // close curl resource to free up system resources
        curl_close($ch);
    }
    public function run(){
        try {
            if( empty($_SERVER['x-authorization']) ) throw new \DirkPersky\Typo3Composer\Exception\Composer();
            header("Access-Control-Allow-Origin: webmanagement.gutenberghaus.de");
            $token = $_SERVER['x-authorization'];
            if( $token != static::$token && crypt($token, static::$token) == static::$token) {
                $this->call($this->get('action'));
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
    protected function get($name){
        if(empty($_POST[$name])) throw new \DirkPersky\Typo3Composer\Exception\Composer();

        return $_POST[$name];
    }
    protected function call($command){
        $config = static::$composer;
        putenv("COMPOSER_HOME={$config['COMPOSER_HOME']}");
        putenv("COMPOSER={$config['COMPOSER']}/composer.json" );
        putenv("OSTYPE={$config['OSTYPE']}"); //force to use php://output instead of php://stdout

        $factory = new \Composer\Factory();
        $output = $factory->createOutput();

        $input = new ArrayInput(array('command' => $command));
        $input->setInteractive(false);

        $application = new Application();
        echo '<pre>';
        $application->doRun($input, $output);
        echo '</pre>';
    }
}