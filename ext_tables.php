<?php
defined('TYPO3_MODE') or die();

(function () {
    TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('
        module.tx_dashboard {
            view {
                templateRootPaths.1594040850 = EXT:matomo_widgets/Resources/Private/Templates/
                partialRootPaths.1594040850 = EXT:matomo_widgets/Resources/Private/Partials/
                layoutRootPaths.1594040850 = EXT:matomo_widgets/Resources/Private/Layouts/
            }
        }
    ');

    /** @var TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
    $iconRegistry = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $iconRegistry->registerIcon(
        'content-widget-matomo',
        TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        [
            'source' => sprintf(
                'EXT:%s/Resources/Public/Icons/content-widget-matomo.svg',
                Brotkrueml\MatomoWidgets\Extension::KEY
            ),
        ]
    );
})();
