<?= '<?php' ?>
// Exit early if php requirement is not satisfied.
if (version_compare(PHP_VERSION, '7.0.0', '<')) {
    die('This version of TYPO3 CMS requires PHP 7.0 or above');
}

// Set up the application for the Frontend
call_user_func(function () {
    $classLoader = require <?= $basePath ?>. '/vendor/autoload.php';
    (new \DirkPersky\Typo3Composer\Classes\Composer($classLoader))->run();
});
