<?php
declare(strict_types=1);
namespace DirkPersky\Typo3Composer\Classes;

use Composer\Console\Application;
use Composer\Script\Event as ScriptEvent;
use Symfony\Component\Console\Input\ArrayInput;

use TYPO3\CMS\Composer\Plugin\Core\InstallerScripts\WebDirectory;

class Install {
    public function __construct() {
        var_dump('COMPOSER_HOME=' . dirname(__DIR__) . '/vendor/bin/composer');
    }


    public static function initProject(ScriptEvent $event){
        var_dump(new WebDirectory());
        var_dump($event);
    }
}