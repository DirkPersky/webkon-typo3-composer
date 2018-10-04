<?php
declare(strict_types=1);
namespace DirkPersky\Typo3Composer\Classes;

use Composer\Console\Application;
use Composer\Script\Event as ScriptEvent;
use Symfony\Component\Console\Input\ArrayInput;

class ChefSymlink
{
    protected static $config;
    protected static $ignore = [
        'sys' => [],
        'vendor' => [
            'typo3/cms-composer-installers',
            'dirkpersky/typo3-composer'
        ]
    ];

    public static function setSymlink(ScriptEvent $event){
        list($basePath, $dir) = static::getPathInfo($event);
        $chefDir = isset(static::$config['chef']) ? static::$config['chef']: null;
        if($chefDir) {
            static::sysExt($chefDir, $basePath, $dir);
            static::vendor($chefDir, $basePath);
        }
    }
    protected static function sysExt($chefDir, $basePath, $publicDir){
        $dir = sprintf('%2$s%1$s%3$stypo3%1$ssysext%1$s',DIRECTORY_SEPARATOR  ,$basePath, $publicDir);
        $glob = glob(sprintf('%1$s*',$dir));
        foreach ($glob as $key => $ext){
            $name = str_replace($dir,'', $ext);
            if( is_dir($ext) && !in_array($name, static::$ignore['sys'])) {
                $chefDirExt = sprintf('%2$s%1$stypo3%1$ssysext%1$s%3$s', DIRECTORY_SEPARATOR, $chefDir, $name);
                if (is_dir($chefDirExt)) {
                    if (!is_link($ext)) static::rmDir($ext);
                    if (is_link($ext))  @unlink($ext);
                    @symlink($chefDirExt, $ext);
                }
            }
        }
    }
    protected static function vendor($chefDir, $basePath) {
        $dir = sprintf('%2$s%1$svendor%1$s',DIRECTORY_SEPARATOR  ,$basePath);
        $glob = glob(sprintf('%1$s*',$dir));
        foreach ($glob as $key => $vendor) {
            if(is_dir($vendor)){
                $name = str_replace($dir,'', $vendor);

                $globVendor = glob(sprintf('%2$s%1$s*',DIRECTORY_SEPARATOR, $vendor));
                foreach ($globVendor as $key2 => $package) {
                    $namePackage = trim(str_replace($vendor,'', $package), DIRECTORY_SEPARATOR);
                    if(is_dir($package)  && !in_array($name.'/'.$namePackage, static::$ignore['vendor'])){
                        $chefDirExt = sprintf('%2$s%1$svendor%1$s%3$s%1$s%4$s', DIRECTORY_SEPARATOR, $chefDir, $name,$namePackage);
                        if (is_dir($chefDirExt)) {
                            if (!is_link($package)) static::rmDir($package);
                            if (is_link($package)) @unlink($package);
                            @symlink($chefDirExt, $package);
                        }
                    }
                }
            }
        }
    }

    protected static function rmDir($dir){
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? static::rmDir("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
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
            static::$config = json_decode($composerJson, true);
        }

        $dir = '';
        // get Public Dir
        if(isset(static::$config['extra']) && isset(static::$config['extra']['typo3/cms']) && isset(static::$config['extra']['typo3/cms']['web-dir'])){
            $dir = static::$config['extra']['typo3/cms']['web-dir'].DIRECTORY_SEPARATOR;
        }

        return [$basePath, $dir];
    }
}
