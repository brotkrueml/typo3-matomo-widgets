<?php
defined('TYPO3_MODE') or die();

if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['matomo_widgets'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['matomo_widgets'] = [
        'frontend' => TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
        'backend' => TYPO3\CMS\Core\Cache\Backend\FileBackend::class,
        'options' => [
            'defaultLifetime' => 3600,
        ],
    ];
}

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['matomoWidgets'] = ['Brotkrueml\\MatomoWidgets\\ViewHelpers'];

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['matomoWidgetsSiteConfigurationMigration']
    = \Brotkrueml\MatomoWidgets\Updates\SiteConfigurationMigration::class;
