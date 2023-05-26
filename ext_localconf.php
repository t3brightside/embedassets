<?php
declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die('Access denied.');

if(!class_exists('\MatthiasMullie\Minify')){
    $composerAutoloadFile = ExtensionManagementUtility::extPath('embedassets')
        . 'Resources/Private/PHP/autoload.php';

    require_once($composerAutoloadFile);
}

