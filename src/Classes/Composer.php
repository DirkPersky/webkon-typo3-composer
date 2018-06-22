<?php
declare(strict_types=1);
namespace DirkPersky\Typo3Composer\Classes;

use Composer\Console\Application;
use Composer\Script\Event as ScriptEvent;
use Symfony\Component\Console\Input\ArrayInput;

use TYPO3\CMS\Composer\Plugin\Core\InstallerScripts\WebDirectory;

class Composer {
    public function __construct() {
        var_dump('COMPOSER_HOME=' . dirname(__DIR__) . '/vendor/bin/composer');
    }


    public static function setVersion(ScriptEvent $event){
        var_dump(new WebDirectory());
        var_dump($event);

        $composer = $event->getComposer();
        $composerConfig = $composer->getConfig();
        $basePath = realpath(substr($composerConfig->get('vendor-dir'), 0, -strlen($composerConfig->get('vendor-dir', $composerConfig::RELATIVE_PATHS))));

        var_dump($basePath);
    }
}