<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Updates;

use Brotkrueml\MatomoWidgets\Configuration\LegacyEnableWidgetsConfigurationFinder;
use Brotkrueml\MatomoWidgets\Updates\EnableWidgetsSiteConfigurationMigration;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Configuration\SiteConfiguration;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

class EnableWidgetsSiteConfigurationMigrationTest extends TestCase
{
    /**
     * @var Stub|LegacyEnableWidgetsConfigurationFinder
     */
    private $legacyConfigurationFinderStub;

    /**
     * @var Stub|SiteFinder
     */
    private $siteFinderStub;

    /**
     * @var Stub|SiteConfiguration
     */
    private $siteConfiguration;

    /**
     * @var EnableWidgetsSiteConfigurationMigration
     */
    private $subject;

    protected function setUp(): void
    {
        $this->legacyConfigurationFinderStub = $this->createStub(LegacyEnableWidgetsConfigurationFinder::class);
        $this->legacyConfigurationFinderStub
            ->method('getEnableWidgetsIdentifier')
            ->willReturn([
                'matomoWidgetsEnableActionsPerDay',
                'matomoWidgetsEnableActionsPerMonth',
                'matomoWidgetsEnableBounceRate',
                'matomoWidgetsEnableBrowsers',
                'matomoWidgetsEnableCampaigns',
                'matomoWidgetsEnableCountries',
                'matomoWidgetsEnableLinkMatomo',
                'matomoWidgetsEnableOsFamilies',
                'matomoWidgetsEnableVisitsPerDay',
                'matomoWidgetsEnableVisitsPerMonth',
            ]);

        $this->siteFinderStub = $this->createStub(SiteFinder::class);
        $this->siteConfiguration = $this->createStub(SiteConfiguration::class);

        $outputDummy = $this->createStub(OutputInterface::class);
        $outputDummy->method('writeln');

        $this->subject = new EnableWidgetsSiteConfigurationMigration(
            $this->legacyConfigurationFinderStub,
            $this->siteFinderStub,
            $this->siteConfiguration
        );
        $this->subject->setOutput($outputDummy);
    }

    /**
     * @test
     */
    public function subjectImplementsUpgradeWizardInterface(): void
    {
        self::assertInstanceOf(UpgradeWizardInterface::class, $this->subject);
    }

    /**
     * @test
     */
    public function executeUpdateMigratesOneConfigurationCorrectly(): void
    {
        $this->legacyConfigurationFinderStub
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([
                'main' => [
                    'matomoWidgetsEnableActionsPerDay' => true,
                    'matomoWidgetsEnableActionsPerMonth' => false,
                    'matomoWidgetsEnableBounceRate' => true,
                    'matomoWidgetsEnableBrowsers' => false,
                    'matomoWidgetsEnableCampaigns' => true,
                    'matomoWidgetsEnableCountries' => false,
                    'matomoWidgetsEnableLinkMatomo' => true,
                    'matomoWidgetsEnableOsFamilies' => false,
                    'matomoWidgetsEnableVisitsPerDay' => true,
                    'matomoWidgetsEnableVisitsPerMonth' => false,
                ],
            ]));

        $this->siteFinderStub
            ->method('getSiteByIdentifier')
            ->with('main')
            ->willReturn(new Site(
                'main',
                42,
                [
                    'base' => 'https://example.org/',
                    'matomoWidgetsEnableActionsPerDay' => true,
                    'matomoWidgetsEnableActionsPerMonth' => false,
                    'matomoWidgetsEnableBounceRate' => true,
                    'matomoWidgetsEnableBrowsers' => false,
                    'matomoWidgetsEnableCampaigns' => true,
                    'matomoWidgetsEnableCountries' => false,
                    'matomoWidgetsEnableLinkMatomo' => true,
                    'matomoWidgetsEnableOsFamilies' => false,
                    'matomoWidgetsEnableVisitsPerDay' => true,
                    'matomoWidgetsEnableVisitsPerMonth' => false,
                    'matomoWidgetsIdSite' => 1,
                    'matomoWidgetsTitle' => 'Demo',
                    'matomoWidgetsTokenAuth' => '',
                    'matomoWidgetsUrl' => 'https://demo.matomo.cloud/',
                    'rootPageId' => 42,
                ]
            ));

        $this->siteConfiguration
            ->method('write')
            ->with('main', [
                'base' => 'https://example.org/',
                'matomoWidgetsIdSite' => 1,
                'matomoWidgetsTitle' => 'Demo',
                'matomoWidgetsTokenAuth' => '',
                'matomoWidgetsUrl' => 'https://demo.matomo.cloud/',
                'rootPageId' => 42,
                'matomoWidgetsActiveWidgets' => 'actionsPerDay,bounceRate,campaigns,linkMatomo,visitsPerDay',
            ]);

        self::assertTrue($this->subject->executeUpdate());
    }

    /**
     * @test
     */
    public function executeUpdateMigratesTwoConfigurationsCorrectly(): void
    {
        $this->legacyConfigurationFinderStub
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([
                'main' => [
                    'matomoWidgetsEnableActionsPerDay' => true,
                    'matomoWidgetsEnableActionsPerMonth' => false,
                    'matomoWidgetsEnableBounceRate' => true,
                    'matomoWidgetsEnableBrowsers' => false,
                    'matomoWidgetsEnableCampaigns' => true,
                    'matomoWidgetsEnableCountries' => false,
                    'matomoWidgetsEnableLinkMatomo' => true,
                    'matomoWidgetsEnableOsFamilies' => false,
                    'matomoWidgetsEnableVisitsPerDay' => true,
                    'matomoWidgetsEnableVisitsPerMonth' => false,
                ],
                'other' => [
                    'matomoWidgetsEnableActionsPerDay' => false,
                    'matomoWidgetsEnableActionsPerMonth' => false,
                    'matomoWidgetsEnableBounceRate' => true,
                    'matomoWidgetsEnableBrowsers' => false,
                    'matomoWidgetsEnableCampaigns' => false,
                    'matomoWidgetsEnableCountries' => false,
                    'matomoWidgetsEnableLinkMatomo' => false,
                    'matomoWidgetsEnableOsFamilies' => false,
                    'matomoWidgetsEnableVisitsPerDay' => false,
                    'matomoWidgetsEnableVisitsPerMonth' => false,
                ],
            ]));

        $siteFinderMap = [
            [
                'main',
                new Site(
                    'main',
                    42,
                    [
                        'base' => 'https://example.org/',
                        'matomoWidgetsEnableActionsPerDay' => true,
                        'matomoWidgetsEnableActionsPerMonth' => false,
                        'matomoWidgetsEnableBounceRate' => true,
                        'matomoWidgetsEnableBrowsers' => false,
                        'matomoWidgetsEnableCampaigns' => true,
                        'matomoWidgetsEnableCountries' => false,
                        'matomoWidgetsEnableLinkMatomo' => true,
                        'matomoWidgetsEnableOsFamilies' => false,
                        'matomoWidgetsEnableVisitsPerDay' => true,
                        'matomoWidgetsEnableVisitsPerMonth' => false,
                        'matomoWidgetsIdSite' => 1,
                        'matomoWidgetsTitle' => 'Demo',
                        'matomoWidgetsTokenAuth' => '',
                        'matomoWidgetsUrl' => 'https://demo.matomo.cloud/',
                        'rootPageId' => 42,
                    ]
                ),
            ],
            [
                'other',
                new Site(
                    'other',
                    84,
                    [
                        'base' => 'https://example.com/',
                        'matomoWidgetsEnableActionsPerDay' => false,
                        'matomoWidgetsEnableActionsPerMonth' => false,
                        'matomoWidgetsEnableBounceRate' => true,
                        'matomoWidgetsEnableBrowsers' => false,
                        'matomoWidgetsEnableCampaigns' => false,
                        'matomoWidgetsEnableCountries' => false,
                        'matomoWidgetsEnableLinkMatomo' => false,
                        'matomoWidgetsEnableOsFamilies' => false,
                        'matomoWidgetsEnableVisitsPerDay' => false,
                        'matomoWidgetsEnableVisitsPerMonth' => false,
                        'matomoWidgetsIdSite' => 2,
                        'matomoWidgetsTitle' => 'Demo 2',
                        'matomoWidgetsTokenAuth' => '',
                        'matomoWidgetsUrl' => 'https://demo.matomo.cloud/',
                        'rootPageId' => 84,
                    ]
                ),
            ],
        ];

        $this->siteFinderStub
            ->method('getSiteByIdentifier')
            ->willReturnMap($siteFinderMap);

        $siteConfigurationMap = [
            [
                'main',
                [
                    'base' => 'https://example.org/',
                    'matomoWidgetsIdSite' => 1,
                    'matomoWidgetsTitle' => 'Demo',
                    'matomoWidgetsTokenAuth' => '',
                    'matomoWidgetsUrl' => 'https://demo.matomo.cloud/',
                    'rootPageId' => 42,
                    'matomoWidgetsActiveWidgets' => 'actionsPerDay,bounceRate,campaigns,linkMatomo,visitsPerDay',
                ],
            ],
            [
                'other',
                [
                    'base' => 'https://example.com/',
                    'matomoWidgetsIdSite' => 2,
                    'matomoWidgetsTitle' => 'Demo',
                    'matomoWidgetsTokenAuth' => '',
                    'matomoWidgetsUrl' => 'https://demo.matomo.cloud/',
                    'rootPageId' => 84,
                    'matomoWidgetsActiveWidgets' => 'bounceRate',
                ],
            ],
        ];

        $this->siteConfiguration
            ->method('write')
            ->willReturnMap($siteConfigurationMap);

        self::assertTrue($this->subject->executeUpdate());
    }

    /**
     * @test
     */
    public function updateNecessaryReturnsFalseIfNoLegacySiteConfigurationIsAvailable(): void
    {
        $this->legacyConfigurationFinderStub
            ->method('count')
            ->willReturn(0);

        self::assertFalse($this->subject->updateNecessary());
    }

    /**
     * @test
     */
    public function updateNecessaryReturnsTrueIfLegacySiteConfigurationIsAvailable(): void
    {
        $this->legacyConfigurationFinderStub
            ->method('count')
            ->willReturn(1);

        self::assertTrue($this->subject->updateNecessary());
    }
}
