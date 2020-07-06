<?php
defined('TYPO3_MODE') or die();

TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('
    module.tx_dashboard {
        view {
            templateRootPaths.1594040850 = EXT:matomo_widgets/Resources/Private/Templates/
            partialRootPaths.1594040850 = EXT:matomo_widgets/Resources/Private/Partials/
            layoutRootPaths.1594040850 = EXT:matomo_widgets/Resources/Private/Layouts/
        }
    }'
);
