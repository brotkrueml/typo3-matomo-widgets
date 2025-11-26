<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Backend;

use Brotkrueml\MatomoWidgets\Configuration\Configurations;
use Brotkrueml\MatomoWidgets\Extension;

/**
 * @internal
 */
final readonly class DashboardPresetsProvider
{
    private const DEFAULT_WIDGETS_TEMPLATES = [
        'visitsPerDay' => 'matomo_widgets.%s.visitsSummary.visitsPerDay',
        'actionsPerDay' => 'matomo_widgets.%s.visitsSummary.actionsPerDay',
        'visitsPerMonth' => 'matomo_widgets.%s.visitsSummary.visitsPerMonth',
        'actionsPerMonth' => 'matomo_widgets.%s.visitsSummary.actionsPerMonth',
        'bounceRate' => 'matomo_widgets.%s.visitsSummary.bounceRate',
        'linkMatomo' => 'matomo_widgets.%s.linkMatomo',
        'browsers' => 'matomo_widgets.%s.devicesDetection.browsers',
        'browserPlugins' => 'matomo_widgets.%s.devicePlugins.plugin',
        'osFamilies' => 'matomo_widgets.%s.devicesDetection.osFamilies',
        'campaigns' => 'matomo_widgets.%s.referrers.campaigns',
        'countries' => 'matomo_widgets.%s.userCountry.country',
    ];

    public function __construct(
        private Configurations $configurations,
    ) {}

    /**
     * @return array<string, array<string, list<string>|string|true>>
     */
    public function getPresets(): array
    {
        $presets = [];
        foreach ($this->configurations as $configuration) {
            $enabledWidgets = \array_values(\array_filter(
                self::DEFAULT_WIDGETS_TEMPLATES,
                $configuration->isWidgetActive(...),
                \ARRAY_FILTER_USE_KEY,
            ));

            if ($enabledWidgets === []) {
                continue;
            }

            $title = \count($this->configurations) > 1
                ? \sprintf('Matomo (%s)', $configuration->siteTitle ?: $configuration->siteIdentifier)
                : 'Matomo';

            $presets['matomo_' . $configuration->siteIdentifier] = [
                'title' => $title,
                'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':preset.description',
                'iconIdentifier' => 'content-dashboard',
                'defaultWidgets' => \array_map(
                    static fn(string $widget): string => \sprintf($widget, $configuration->siteIdentifier),
                    $enabledWidgets,
                ),
                'showInWizard' => true,
            ];
        }

        return $presets;
    }
}
