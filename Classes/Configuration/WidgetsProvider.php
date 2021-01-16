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
    /** @var array<string,string> */
    private $availableWidgets = [
        'matomoWidgetsEnableActionsPerDay' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.actionsPerDay.title',
        'matomoWidgetsEnableActionsPerMonth' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.actionsPerMonth.title',
        'matomoWidgetsEnableVisitsPerDay' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.visitsPerDay.title',
        'matomoWidgetsEnableVisitsPerMonth' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.visitsPerMonth.title',
        'matomoWidgetsEnableBounceRate' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.bounceRate.title',
        'matomoWidgetsEnableBrowsers' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.devicesDetection.browsers.title',
        'matomoWidgetsEnableOsFamilies' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.devicesDetection.osFamilies.title',
        'matomoWidgetsEnableCampaigns' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.referrers.campaigns.title',
        'matomoWidgetsEnableCountries' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.userCountry.country.title',
        'matomoWidgetsEnableLinkMatomo' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.linkMatomo.title',
    ];

    public function getWidgetConfigurationKeys(): array
    {
        return \array_keys($this->availableWidgets);
    }

    public function getTitleForWidget(string $widgetConfigurationKey): string
    {
        return $this->availableWidgets[$widgetConfigurationKey] ?? '';
    }
}
