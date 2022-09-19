<?php
defined('TYPO3') or die();

use TYPO3\CMS\Core\Cache\Backend\FileBackend;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;

$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['matomo_widgets'] ??= [
    'frontend' => VariableFrontend::class,
    'backend' => FileBackend::class,
    'options' => [
        'defaultLifetime' => 3600,
    ],
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['matomoWidgets'] = ['Brotkrueml\\MatomoWidgets\\ViewHelpers'];
