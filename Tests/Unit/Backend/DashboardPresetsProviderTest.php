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
use Brotkrueml\MatomoWidgets\Configuration\Configurations;
use PHPUnit\Framework\TestCase;

class DashboardPresetsProviderTest extends TestCase
{
    /**
     * @test
     */
    public function getPresetsReturnsEmptyArrayIfNoConfigurationIsAvailable(): void
    {
        $subject = new DashboardPresetsProvider(new Configurations([]));

        self::assertSame([], $subject->getPresets());
    }

    /**
     * @test
     */
    public function getPresetsReturnsOnePresetCorrectlyIfOneConfigurationIsAvailable(): void
    {
        $configurations = new Configurations([
            new Configuration(
                'some_site',
                'Some Site',
                'https://example.org/',
                42,
                '',
                [
                    'visitsPerDay',
                ],
                [],
                ''
            ),
        ]);

        $subject = new DashboardPresetsProvider($configurations);

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
        $configurations = new Configurations([
            new Configuration(
                'some_site',
                'Some Site',
                'https://example.org/',
                42,
                '',
                [
                    'visitsPerDay',
                ],
                [],
                ''
            ),
            new Configuration(
                'another_site',
                'Another Site',
                'https://example.com/',
                123,
                '',
                [
                    'visitsPerDay',
                    'actionsPerDay',
                ],
                [],
                ''
            ),
        ]);

        $subject = new DashboardPresetsProvider($configurations);

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
        $configurations = new Configurations([
            new Configuration(
                'some_site',
                'Some Site',
                'https://example.org/',
                42,
                '',
                [
                    'actionsPerDay',
                    'actionsPerMonth',
                    'bounceRate',
                    'browserPlugins',
                    'browsers',
                    'campaigns',
                    'countries',
                    'linkMatomo',
                    'osFamilies',
                    'visitsPerMonth',
                    'visitsPerDay',
                ],
                [],
                ''
            ),
        ]);

        $subject = new DashboardPresetsProvider($configurations);

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
                    'matomo_widgets.some_site.devicePlugins.plugin',
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
        $configurations = new Configurations([
            new Configuration(
                'some_site',
                'Some Site',
                'https://example.org/',
                42,
                '',
                [
                    'actionsPerDay' => false,
                    'actionsPerMonth' => false,
                    'bounceRate' => false,
                    'browsers' => false,
                    'campaigns' => false,
                    'countries' => false,
                    'linkMatomo' => false,
                    'osFamilies' => false,
                    'visitsPerMonth' => false,
                    'visitsPerDay' => false,
                ],
                [],
                ''
            ),
        ]);

        $subject = new DashboardPresetsProvider($configurations);

        self::assertSame([], $subject->getPresets());
    }
}
