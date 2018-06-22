<?php
declare(strict_types=1);
namespace DirkPersky\Typo3Composer\Classes;

use Composer\Console\Application;
use Composer\Script\Event as ScriptEvent;
use Symfony\Component\Console\Input\ArrayInput;

class ChefSymlink
{
    protected static $config;

    public static function setSymlink(ScriptEvent $event){
        if(!static::getConfig($event)) return false;

        $chefDir = isset(static::$config['chef']) ? static::$config: null;
    }
    protected static function getConfig(ScriptEvent $event){
        if( Composer::$composer ) {
            $basePath = Composer::$composer['COMPOSER'];
        } else {
            $composer = $event->getComposer();
            $composerConfig = $composer->getConfig();

            $basePath = realpath(substr($composerConfig->get('vendor-dir'), 0, -strlen($composerConfig->get('vendor-dir', $composerConfig::RELATIVE_PATHS))));
        }

        $composerJson = file_get_contents(sprintf('%1$s/composer.json', $basePath));
        if($composerJson) {
            static::$config = json_decode($composerJson, true);
            return true;
        }

        return false;
    }

}
