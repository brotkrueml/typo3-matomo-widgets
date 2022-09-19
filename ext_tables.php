<?php
defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addTypoScriptSetup('
    module.tx_dashboard {
        view {
            templateRootPaths.1594040850 = EXT:matomo_widgets/Resources/Private/Templates/
            partialRootPaths.1594040850 = EXT:matomo_widgets/Resources/Private/Partials/
            layoutRootPaths.1594040850 = EXT:matomo_widgets/Resources/Private/Layouts/
        }
    }
');
