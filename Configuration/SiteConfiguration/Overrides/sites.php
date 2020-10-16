<?php

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

(function () {
    $GLOBALS['SiteConfiguration']['site']['columns'] += [
        'matomoWidgetsTitle' => [
            'label' => \Brotkrueml\MatomoWidgets\Extension::LANGUAGE_PATH_SITECONF . ':title',
            'description' => \Brotkrueml\MatomoWidgets\Extension::LANGUAGE_PATH_SITECONF . ':title.description',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
            ],
        ],
        'matomoWidgetsUrl' => [
            'label' => \Brotkrueml\MatomoWidgets\Extension::LANGUAGE_PATH_SITECONF . ':url',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
            ],
        ],
        'matomoWidgetsIdSite' => [
            'label' => \Brotkrueml\MatomoWidgets\Extension::LANGUAGE_PATH_SITECONF . ':idSite',
            'config' => [
                'type' => 'input',
                'eval' => 'int',
            ],
        ],
        'matomoWidgetsTokenAuth' => [
            'label' => \Brotkrueml\MatomoWidgets\Extension::LANGUAGE_PATH_SITECONF . ':tokenAuth',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
            ],
        ],
    ];

    $availableWidgetsProvider = new \Brotkrueml\MatomoWidgets\Configuration\WidgetsProvider();
    $availableWidgets = $availableWidgetsProvider->getWidgetConfigurationKeys();
    foreach ($availableWidgets as $widget) {
        $GLOBALS['SiteConfiguration']['site']['columns'] += [
            $widget => [
                'label' => $availableWidgetsProvider->getTitleForWidget($widget),
                'config' => [
                    'type' => 'check',
                    'renderType' => 'checkboxLabeledToggle',
                    'items' => [
                        [
                            0 => '',
                            1 => '',
                            'labelChecked' => 'Enabled',
                            'labelUnchecked' => 'Disabled',
                        ],
                    ],
                    'default' => 1,
                ],
            ],
        ];
    }

    $GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] .= '
        ,
        --div--;' . \Brotkrueml\MatomoWidgets\Extension::LANGUAGE_PATH_SITECONF . ':matomoWidgets,
        matomoWidgetsTitle,
        --palette--;;matomoWidgetsInstallation,
        --palette--;;matomoWidgetsActiveWidgets,
    ';

    $GLOBALS['SiteConfiguration']['site']['palettes'] += [
        'matomoWidgetsInstallation' => [
            'label' => \Brotkrueml\MatomoWidgets\Extension::LANGUAGE_PATH_SITECONF . ':matomoInstallation',
            'showitem' => 'matomoWidgetsUrl, matomoWidgetsIdSite, matomoWidgetsTokenAuth',
        ],
        'matomoWidgetsActiveWidgets' => [
            'label' => \Brotkrueml\MatomoWidgets\Extension::LANGUAGE_PATH_SITECONF . ':enabledWidgets',
            'showitem' => implode(',', $availableWidgets),
        ],
    ];
})();
