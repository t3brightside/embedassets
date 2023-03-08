<?php
defined('TYPO3') || die('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][TYPO3\CMS\Fluid\ViewHelpers\Asset\CssViewHelper::class] = [
    'className' => \Brightside\Embedassets\Xclass\CssViewHelper::class
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][TYPO3\CMS\Fluid\ViewHelpers\Asset\ScriptViewHelper::class] = [
    'className' => \Brightside\Embedassets\Xclass\ScriptViewHelper::class
];
