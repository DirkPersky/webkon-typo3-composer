<?php
declare(strict_types=1);
namespace DirkPersky\Typo3Composer\Classes;

use Composer\Console\Application;
use Composer\Script\Event as ScriptEvent;
use Symfony\Component\Console\Input\ArrayInput;

class Composer {
    static $composer;

    public function __construct($loader) {
        $selfPath = $loader->findFile('DirkPersky\\Typo3Composer\\Classes\\Composer');
        preg_match("/^(.*?)vendor/", $selfPath, $matches);
        list($vendor, $base) = $matches;

        static::$composer = [
            'COMPOSER_HOME' => $vendor .'/bin/composer',
            'COMPOSER' => $base,
            'OSTYPE' => 'OS400',
        ];
    }
    public function run(){
        if($this->hasAccess()) $this->call($this->get('action'));
    }
    protected function hasAccess(){
        if(isset($_SERVER['x-authorization'])) {
            header("Access-Control-Allow-Origin: webmanagement.gutenberghaus.de");
            $token = $_SERVER['x-authorization'];
            $crypt = '$1$Tq78lmeW$1UBxHRze56fuvFf5rr4lJ.';
            if( $token != $crypt && crypt($token, $crypt) == $crypt) return true;
        }

        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        header($protocol . ' ' . 404 . ' Page Not Found');
        require_once dirname(__DIR__).'/view/404.php';
        exit;
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

    public static function removeVersion(ScriptEvent $event){
        list($basePath, $dir) = static::getPathInfo($event);
        unlink(sprintf('%2$s%1$s%3$scomposer.php',DIRECTORY_SEPARATOR  ,$basePath, $dir));
    }
    public static function setVersion(ScriptEvent $event){
        list($basePath, $dir) = static::getPathInfo($event);
        // Copy File to Public dir
        copy(dirname(__DIR__).DIRECTORY_SEPARATOR.'composer.php', sprintf('%2$s%1$s%3$scomposer.php',DIRECTORY_SEPARATOR  ,$basePath, $dir));
    }
    protected static function getPathInfo(ScriptEvent $event){
        if(!static::$composer ) {
            $composer = $event->getComposer();
            $composerConfig = $composer->getConfig();

            $basePath = realpath(substr($composerConfig->get('vendor-dir'), 0, -strlen($composerConfig->get('vendor-dir', $composerConfig::RELATIVE_PATHS))));
        } else {
            $basePath = static::$composer['COMPOSER'];
        }

        $composerJson = file_get_contents(sprintf('%1$s/composer.json', $basePath));
        if($composerJson) {
            $composerJson = json_decode($composerJson, true);
        }
        $dir = '';
        // get Public Dir
        if(isset($composerJson['extra']) && isset($composerJson['extra']['typo3/cms']) && isset($composerJson['extra']['typo3/cms']['web-dir'])){
            $dir = $composerJson['extra']['typo3/cms']['web-dir'].DIRECTORY_SEPARATOR;
        }

        return [$basePath, $dir];
    }
}
