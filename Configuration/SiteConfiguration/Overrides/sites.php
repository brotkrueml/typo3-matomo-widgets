<?php

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

$GLOBALS['SiteConfiguration']['site']['columns'] += [
    'matomoWidgetsTitle' => [
        'label' => Brotkrueml\MatomoWidgets\Extension::LANGUAGE_PATH_SITECONF . ':title',
        'description' => Brotkrueml\MatomoWidgets\Extension::LANGUAGE_PATH_SITECONF . ':title.description',
        'config' => [
            'type' => 'input',
            'eval' => 'trim',
        ],
    ],
    'matomoWidgetsUrl' => [
        'label' => Brotkrueml\MatomoWidgets\Extension::LANGUAGE_PATH_SITECONF . ':url',
        'config' => [
            'type' => 'input',
            'eval' => 'trim',
        ],
    ],
    'matomoWidgetsIdSite' => [
        'label' => Brotkrueml\MatomoWidgets\Extension::LANGUAGE_PATH_SITECONF . ':idSite',
        'config' => [
            'type' => 'input',
            'eval' => 'int',
        ],
    ],
    'matomoWidgetsTokenAuth' => [
        'label' => Brotkrueml\MatomoWidgets\Extension::LANGUAGE_PATH_SITECONF . ':tokenAuth',
        'config' => [
            'type' => 'input',
            'eval' => 'trim',
        ],
    ],
    'matomoWidgetsActiveWidgets' => [
        'label' => Brotkrueml\MatomoWidgets\Extension::LANGUAGE_PATH_SITECONF . ':activeWidgets',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectCheckBox',
            'items' => (new Brotkrueml\MatomoWidgets\Configuration\WidgetsProvider())->getItemsForTca(),
        ],
    ],
    'matomoWidgetsPagesNotFoundTemplate' => [
        'label' => Brotkrueml\MatomoWidgets\Extension::LANGUAGE_PATH_SITECONF . ':pagesNotFoundTemplate',
        'description' => Brotkrueml\MatomoWidgets\Extension::LANGUAGE_PATH_SITECONF . ':pagesNotFoundTemplate.description',
        'config' => [
            'type' => 'input',
            'eval' => 'trim',
            'default' => '404/URL = {path}/From = {referrer}',
        ],
    ],
];

if (TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('matomo_integration')) {
    $GLOBALS['SiteConfiguration']['site']['columns'] += [
        'matomoWidgetsConsiderMatomoIntegration' => [
            'label' => Brotkrueml\MatomoWidgets\Extension::LANGUAGE_PATH_SITECONF . ':considerMatomoIntegration',
            'description' => Brotkrueml\MatomoWidgets\Extension::LANGUAGE_PATH_SITECONF . ':considerMatomoIntegration.description',
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

    if ((new TYPO3\CMS\Core\Information\Typo3Version())->getMajorVersion() < 12) {
        $GLOBALS['SiteConfiguration']['site']['columns']['matomoWidgetsConsiderMatomoIntegration']['config']['items'][0][0]
            = $GLOBALS['SiteConfiguration']['site']['columns']['matomoWidgetsConsiderMatomoIntegration']['config']['items'][0]['label'];
        $GLOBALS['SiteConfiguration']['site']['columns']['matomoWidgetsConsiderMatomoIntegration']['config']['items'][0][1]
            = $GLOBALS['SiteConfiguration']['site']['columns']['matomoWidgetsConsiderMatomoIntegration']['config']['items'][0]['value'];
        unset($GLOBALS['SiteConfiguration']['site']['columns']['matomoWidgetsConsiderMatomoIntegration']['config']['items'][0]['label']);
        unset($GLOBALS['SiteConfiguration']['site']['columns']['matomoWidgetsConsiderMatomoIntegration']['config']['items'][0]['value']);
    }
}

$GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] .= ',
    --div--;' . Brotkrueml\MatomoWidgets\Extension::LANGUAGE_PATH_SITECONF . ':matomoWidgets,
    matomoWidgetsTitle,
    --palette--;;matomoWidgetsInstallation,
    --palette--;;matomoWidgetsActiveWidgets,
';

$GLOBALS['SiteConfiguration']['site']['palettes'] += [
    'matomoWidgetsInstallation' => [
        'label' => Brotkrueml\MatomoWidgets\Extension::LANGUAGE_PATH_SITECONF . ':matomoInstallation',
        'showitem' =>
            (
                TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('matomo_integration')
                    ? 'matomoWidgetsConsiderMatomoIntegration, --linebreak--, '
                    : ''
            ) . 'matomoWidgetsUrl, matomoWidgetsIdSite, matomoWidgetsTokenAuth',
    ],
    'matomoWidgetsActiveWidgets' => [
        'label' => Brotkrueml\MatomoWidgets\Extension::LANGUAGE_PATH_SITECONF . ':dashboardWidgets',
        'showitem' => 'matomoWidgetsActiveWidgets, --linebreak--, matomoWidgetsPagesNotFoundTemplate,',
    ],
];
