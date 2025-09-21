<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Configuration;

use Brotkrueml\MatomoWidgets\Extension;

/**
 * @internal
 */
final class WidgetsProvider
{
    /**
     * @var array<string,string>
     */
    private array $availableWidgets = [
        'actionsPerDay' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.actionsPerDay.title',
        'actionsPerMonth' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.actionsPerMonth.title',
        'annotations' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.annotations.title',
        'conversionsPerMonth' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.goals.conversionsPerMonth.title',
        'createAnnotation' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.createAnnotation.title',
        'bounceRate' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.bounceRate.title',
        'browserPlugins' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.devicePlugins.plugin.title',
        'browsers' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.devicesDetection.browsers.title',
        'campaigns' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.referrers.campaigns.title',
        'contentNames' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.contents.contentNames.title',
        'contentPieces' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.contents.contentPieces.title',
        'countries' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.userCountry.country.title',
        'javaScriptErrors' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.events.javaScriptErrors.title',
        'linkMatomo' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.linkMatomo.title',
        'mostViewedPages' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.actions.mostViewedPages.title',
        'osFamilies' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.devicesDetection.osFamilies.title',
        'pagesNotFound' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.actions.pagesNotFound.title',
        'siteSearchKeywords' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.actions.siteSearchKeywords.title',
        'siteSearchNoResultKeywords' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.actions.siteSearchNoResultKeywords.title',
        'visitsPerDay' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.visitsPerDay.title',
        'visitsPerMonth' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.visitsPerMonth.title',
    ];

    /**
     * @return array<non-empty-array<0|1|'label'|'value', string>>
     */
    public function getItemsForTca(): array
    {
        $items = [];
        foreach ($this->availableWidgets as $identifier => $languageKey) {
            $items[] = [
                'label' => $languageKey,
                'value' => $identifier,
            ];
        }

        return $items;
    }
}
