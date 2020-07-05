<?php

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

return [
    'matomo' => [
        'title' => 'LLL:EXT:matomo_widgets/Resources/Private/Language/Dashboard.xlf:preset.title',
        'description' => 'LLL:EXT:matomo_widgets/Resources/Private/Language/Dashboard.xlf:preset.description',
        'iconIdentifier' => 'content-dashboard',
        'defaultWidgets' => [
            'matomo_widgets.visitsSummary.visitsPerDay',
            'matomo_widgets.visitsSummary.actionsPerDay',
            'matomo_widgets.visitsSummary.visitsPerMonth',
            'matomo_widgets.visitsSummary.actionsPerMonth',
            'matomo_widgets.visitsSummary.bounceRate',
            'matomo_widgets.linkMatomo',
        ],
        'showInWizard' => true
    ],
];
