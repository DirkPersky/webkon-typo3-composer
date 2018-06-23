<?php
declare(strict_types=1);
namespace DirkPersky\Typo3Composer\Classes;

use Composer\Console\Application;
use Composer\Script\Event as ScriptEvent;
use Symfony\Component\Console\Input\ArrayInput;

class Installer {

    public static function removeVersion(ScriptEvent $event){
        list($basePath, $dir) = static::getPathInfo($event);
        unlink(sprintf('%2$s%1$s%3$scomposer.php',DIRECTORY_SEPARATOR  ,$basePath, $dir));
    }
    public static function setVersion(ScriptEvent $event){
        list($basePath, $dir) = static::getPathInfo($event);

        ob_start();
        require_once dirname(__DIR__).'/View/composer.php';
        $content = ob_get_contents();
        ob_clean();
        // Copy File to Public dir
        file_put_contents( sprintf('%2$s%1$s%3$scomposer.php',DIRECTORY_SEPARATOR  ,$basePath, $dir), $content);
    }
    protected static function getPathInfo(ScriptEvent $event){
        if( Composer::$composer ) {
            $basePath = Composer::$composer['COMPOSER'];
        } else {
            $composer = $event->getComposer();
            $composerConfig = $composer->getConfig();

            $basePath = realpath(substr($composerConfig->get('vendor-dir'), 0, -strlen($composerConfig->get('vendor-dir', $composerConfig::RELATIVE_PATHS))));
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