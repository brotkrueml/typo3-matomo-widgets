<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Backend;

use Brotkrueml\MatomoWidgets\Backend\DashboardPresetsProvider;
use Brotkrueml\MatomoWidgets\Configuration\Configuration;
use PHPUnit\Framework\TestCase;

class DashboardPresetsProviderTest extends TestCase
{
    /**
     * @test
     */
    public function getPresetsReturnsEmptyArrayIfNoConfigurationIsAvailable(): void
    {
        $subject = new DashboardPresetsProvider([]);

        self::assertSame([], $subject->getPresets());
    }

    /**
     * @test
     */
    public function getPresetsReturnsOnePresetCorrectlyIfOneConfigurationIsAvailable(): void
    {
        $configurationFinder = [
            new Configuration(
                'some_site',
                'Some Site',
                'https://example.org/',
                42,
                '',
                [
                    'matomoWidgetsEnableVisitsPerDay' => true,
                    'matomoWidgetsEnableActionsPerDay' => false,
                ]
            ),
        ];

        $subject = new DashboardPresetsProvider($configurationFinder);

        $expected = [
            'matomo_some_site' => [
                'title' => 'Matomo',
                'description' => 'LLL:EXT:matomo_widgets/Resources/Private/Language/Dashboard.xlf:preset.description',
                'iconIdentifier' => 'content-dashboard',
                'defaultWidgets' => [
                    'matomo_widgets.some_site.visitsSummary.visitsPerDay',
                ],
                'showInWizard' => true,
            ],
        ];

        self::assertSame($expected, $subject->getPresets());
    }

    /**
     * @test
     */
    public function getPresetsReturnsTwoPresetsCorrectlyIfTwoConfigurationsAreAvailable(): void
    {
        $configurationFinder = [
            new Configuration(
                'some_site',
                'Some Site',
                'https://example.org/',
                42,
                '',
                [
                    'matomoWidgetsEnableVisitsPerDay' => true,
                    'matomoWidgetsEnableActionsPerDay' => false,
                ]
            ),
            new Configuration(
                'another_site',
                'Another Site',
                'https://example.com/',
                123,
                '',
                [
                    'matomoWidgetsEnableVisitsPerDay' => true,
                    'matomoWidgetsEnableActionsPerDay' => true,
                ]
            ),
        ];

        $subject = new DashboardPresetsProvider($configurationFinder);

        $expected = [
            'matomo_some_site' => [
                'title' => 'Matomo (Some Site)',
                'description' => 'LLL:EXT:matomo_widgets/Resources/Private/Language/Dashboard.xlf:preset.description',
                'iconIdentifier' => 'content-dashboard',
                'defaultWidgets' => [
                    'matomo_widgets.some_site.visitsSummary.visitsPerDay',
                ],
                'showInWizard' => true,
            ],
            'matomo_another_site' => [
                'title' => 'Matomo (Another Site)',
                'description' => 'LLL:EXT:matomo_widgets/Resources/Private/Language/Dashboard.xlf:preset.description',
                'iconIdentifier' => 'content-dashboard',
                'defaultWidgets' => [
                    'matomo_widgets.another_site.visitsSummary.visitsPerDay',
                    'matomo_widgets.another_site.visitsSummary.actionsPerDay',
                ],
                'showInWizard' => true,
            ],
        ];

        self::assertSame($expected, $subject->getPresets());
    }

    /**
     * @test
     */
    public function getPresetsReturnsAllWidgetsIfAllAreEnabled(): void
    {
        $configurationFinder = [
            new Configuration(
                'some_site',
                'Some Site',
                'https://example.org/',
                42,
                '',
                [
                    'matomoWidgetsEnableActionsPerDay' => true,
                    'matomoWidgetsEnableActionsPerMonth' => true,
                    'matomoWidgetsEnableBounceRate' => true,
                    'matomoWidgetsEnableBrowsers' => true,
                    'matomoWidgetsEnableCampaigns' => true,
                    'matomoWidgetsEnableCountries' => true,
                    'matomoWidgetsEnableLinkMatomo' => true,
                    'matomoWidgetsEnableOsFamilies' => true,
                    'matomoWidgetsEnableVisitsPerMonth' => true,
                    'matomoWidgetsEnableVisitsPerDay' => true,
                ]
            ),
        ];

        $subject = new DashboardPresetsProvider($configurationFinder);

        $expected = [
            'matomo_some_site' => [
                'title' => 'Matomo',
                'description' => 'LLL:EXT:matomo_widgets/Resources/Private/Language/Dashboard.xlf:preset.description',
                'iconIdentifier' => 'content-dashboard',
                'defaultWidgets' => [
                    'matomo_widgets.some_site.visitsSummary.visitsPerDay',
                    'matomo_widgets.some_site.visitsSummary.actionsPerDay',
                    'matomo_widgets.some_site.visitsSummary.visitsPerMonth',
                    'matomo_widgets.some_site.visitsSummary.actionsPerMonth',
                    'matomo_widgets.some_site.visitsSummary.bounceRate',
                    'matomo_widgets.some_site.linkMatomo',
                    'matomo_widgets.some_site.devicesDetection.browsers',
                    'matomo_widgets.some_site.devicesDetection.osFamilies',
                    'matomo_widgets.some_site.referrers.campaigns',
                    'matomo_widgets.some_site.userCountry.country',
                ],
                'showInWizard' => true,
            ],
        ];

        self::assertSame($expected, $subject->getPresets());
    }

    /**
     * @test
     */
    public function getPresetsIgnoresPresetIfAllWidgetsAreDisabled(): void
    {
        $configurationFinder = [
            new Configuration(
                'some_site',
                'Some Site',
                'https://example.org/',
                42,
                '',
                [
                    'matomoWidgetsEnableActionsPerDay' => false,
                    'matomoWidgetsEnableActionsPerMonth' => false,
                    'matomoWidgetsEnableBounceRate' => false,
                    'matomoWidgetsEnableBrowsers' => false,
                    'matomoWidgetsEnableCampaigns' => false,
                    'matomoWidgetsEnableCountries' => false,
                    'matomoWidgetsEnableLinkMatomo' => false,
                    'matomoWidgetsEnableOsFamilies' => false,
                    'matomoWidgetsEnableVisitsPerMonth' => false,
                    'matomoWidgetsEnableVisitsPerDay' => false,
                ]
            ),
        ];

        $subject = new DashboardPresetsProvider($configurationFinder);

        self::assertSame([], $subject->getPresets());
    }
}
