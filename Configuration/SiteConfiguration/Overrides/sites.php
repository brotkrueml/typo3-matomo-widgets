<?php

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\MatomoWidgets\Configuration\Widgets;
use Brotkrueml\MatomoWidgets\Extension;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$GLOBALS['SiteConfiguration']['site']['columns'] += [
    'matomoWidgetsTitle' => [
        'label' => Extension::LANGUAGE_PATH_SITECONF . ':title',
        'description' => Extension::LANGUAGE_PATH_SITECONF . ':title.description',
        'config' => [
            'type' => 'input',
            'eval' => 'trim',
        ],
    ],
    'matomoWidgetsUrl' => [
        'label' => Extension::LANGUAGE_PATH_SITECONF . ':url',
        'config' => [
            'type' => 'input',
            'eval' => 'trim',
        ],
    ],
    'matomoWidgetsIdSite' => [
        'label' => Extension::LANGUAGE_PATH_SITECONF . ':idSite',
        'config' => [
            'type' => 'input',
            'eval' => 'int',
        ],
    ],
    'matomoWidgetsTokenAuth' => [
        'label' => Extension::LANGUAGE_PATH_SITECONF . ':tokenAuth',
        'config' => [
            'type' => 'input',
            'eval' => 'trim',
        ],
    ],
    'matomoWidgetsActiveWidgets' => [
        'label' => Extension::LANGUAGE_PATH_SITECONF . ':activeWidgets',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectCheckBox',
            'items' => Widgets::getItemsForSiteConfiguration(),
        ],
    ],
    'matomoWidgetsPagesNotFoundTemplate' => [
        'label' => Extension::LANGUAGE_PATH_SITECONF . ':pagesNotFoundTemplate',
        'description' => Extension::LANGUAGE_PATH_SITECONF . ':pagesNotFoundTemplate.description',
        'config' => [
            'type' => 'input',
            'eval' => 'trim',
            'default' => '404/URL = {path} /From = {referrer}',
        ],
    ],
];

if (ExtensionManagementUtility::isLoaded('matomo_integration')) {
    $GLOBALS['SiteConfiguration']['site']['columns'] += [
        'matomoWidgetsConsiderMatomoIntegration' => [
            'label' => Extension::LANGUAGE_PATH_SITECONF . ':considerMatomoIntegration',
            'description' => Extension::LANGUAGE_PATH_SITECONF . ':considerMatomoIntegration.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [[
                    'label' => '',
                    'value' => '',
                ]],
            ],
            'onChange' => 'reload',
        ],
    ];
    $GLOBALS['SiteConfiguration']['site']['columns']['matomoWidgetsUrl']['displayCond'] = 'FIELD:matomoWidgetsConsiderMatomoIntegration:REQ:false';
    $GLOBALS['SiteConfiguration']['site']['columns']['matomoWidgetsIdSite']['displayCond'] = 'FIELD:matomoWidgetsConsiderMatomoIntegration:REQ:false';
    $GLOBALS['SiteConfiguration']['site']['columns']['matomoWidgetsPagesNotFoundTemplate']['displayCond'] = 'FIELD:matomoWidgetsConsiderMatomoIntegration:REQ:false';
}

$GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] .= ',
    --div--;' . Extension::LANGUAGE_PATH_SITECONF . ':matomoWidgets,
    matomoWidgetsTitle,
    --palette--;;matomoWidgetsInstallation,
    --palette--;;matomoWidgetsActiveWidgets,
';

$GLOBALS['SiteConfiguration']['site']['palettes'] += [
    'matomoWidgetsInstallation' => [
        'label' => Extension::LANGUAGE_PATH_SITECONF . ':matomoInstallation',
        'showitem'
            => (
                ExtensionManagementUtility::isLoaded('matomo_integration')
                    ? 'matomoWidgetsConsiderMatomoIntegration, --linebreak--, '
                    : ''
            ) . 'matomoWidgetsUrl, matomoWidgetsIdSite, matomoWidgetsTokenAuth',
    ],
    'matomoWidgetsActiveWidgets' => [
        'label' => Extension::LANGUAGE_PATH_SITECONF . ':dashboardWidgets',
        'showitem' => 'matomoWidgetsActiveWidgets, --linebreak--, matomoWidgetsPagesNotFoundTemplate,',
    ],
];
