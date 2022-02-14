<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Backend;

use Brotkrueml\MatomoWidgets\Adapter\ExtensionAvailability;
use Brotkrueml\MatomoWidgets\Configuration\Configuration;
use Brotkrueml\MatomoWidgets\Configuration\ConfigurationFinder;
use Brotkrueml\MatomoWidgets\Extension;
use TYPO3\CMS\Core\Core\Environment;

/**
 * @internal
 */
final class DashboardPresetsProvider
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

    /**
     * @var ConfigurationFinder
     */
    private $configurationFinder;

    /**
     * @param ConfigurationFinder|null $configurationFinder For testing purposes only
     */
    public function __construct($configurationFinder = null)
    {
        $this->configurationFinder = $configurationFinder
            ?? new ConfigurationFinder(
                Environment::getConfigPath(),
                (new ExtensionAvailability())->isMatomoIntegrationAvailable()
            );
    }

    /**
     * @return array<string, array<string, list<string>|string|true>>
     */
    public function getPresets(): array
    {
        $presets = [];
        foreach ($this->configurationFinder as $configuration) {
            /** @var Configuration $configuration */
            $enabledWidgets = \array_values(\array_filter(
                self::DEFAULT_WIDGETS_TEMPLATES,
                static function (string $widgetConfigurationKey) use ($configuration): bool {
                    return $configuration->isWidgetActive($widgetConfigurationKey);
                },
                \ARRAY_FILTER_USE_KEY
            ));

            if ($enabledWidgets === []) {
                continue;
            }

            $title = \count($this->configurationFinder) > 1
                ? \sprintf('Matomo (%s)', $configuration->getSiteTitle() ?: $configuration->getSiteIdentifier())
                : 'Matomo';

            /** @phpstan-ignore-next-line */
            $presets['matomo_' . $configuration->getSiteIdentifier()] = [
                'title' => $title,
                'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':preset.description',
                'iconIdentifier' => 'content-dashboard',
                'defaultWidgets' => \array_map(static function (string $widget) use ($configuration): string {
                    return \sprintf($widget, $configuration->getSiteIdentifier());
                }, $enabledWidgets),
                'showInWizard' => true,
            ];
        }

        return $presets;
    }
}
