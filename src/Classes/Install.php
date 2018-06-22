<?php

namespace DirkPersky\Typo3Composer\Classes;
use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

class Install {
    public function __construct() {
        var_dump('COMPOSER_HOME=' . dirname(__DIR__) . '/vendor/bin/composer');
    }
}