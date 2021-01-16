<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Updates;

use Brotkrueml\MatomoWidgets\Configuration\ConfigurationFinder;
use Brotkrueml\MatomoWidgets\Updates\SiteConfigurationMigration;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Configuration\SiteConfiguration;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

class SiteConfigurationMigrationTest extends TestCase
{
    /** @var MockObject|ConfigurationFinder */
    private $configurationFinderMock;

    /** @var MockObject|ExtensionConfiguration */
    private $extensionConfigurationMock;

    /** @var MockObject|SiteFinder */
    private $siteFinderMock;

    /** @var SiteConfigurationMigration */
    private $subject;

    protected function setUp(): void
    {
        $this->configurationFinderMock = $this->createMock(ConfigurationFinder::class);
        $this->extensionConfigurationMock = $this->createMock(ExtensionConfiguration::class);
        $this->siteFinderMock = $this->createMock(SiteFinder::class);

        $outputDummy = $this->createStub(OutputInterface::class);
        $outputDummy->method('writeln');

        $this->subject = new SiteConfigurationMigration(
            $this->configurationFinderMock,
            $this->extensionConfigurationMock,
            $this->siteFinderMock
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
    public function executeUpdateMigratesTheConfigurationCorrectly(): void
    {
        $this->extensionConfigurationMock
            ->expects(self::once())
            ->method('get')
            ->willreturn([
                'idSite' => '1',
                'tokenAuth' => 'someauthtoken',
                'url' => 'https://example.org/',
            ]);

        $this->siteFinderMock
            ->expects(self::once())
            ->method('getAllSites')
            ->willReturn([new Site('some_site', 1, ['base' => 'https://example.org/'])]);

        $siteConfigurationMock = $this->createMock(SiteConfiguration::class);
        GeneralUtility::setSingletonInstance(SiteConfiguration::class, $siteConfigurationMock);

        $siteConfigurationMock
            ->expects(self::once())
            ->method('write')
            ->with(
                'some_site',
                [
                    'base' => 'https://example.org/',
                    'matomoWidgetsIdSite' => 1,
                    'matomoWidgetsTitle' => '',
                    'matomoWidgetsTokenAuth' => 'someauthtoken',
                    'matomoWidgetsUrl' => 'https://example.org/',
                    'matomoWidgetsEnableActionsPerDay' => true,
                    'matomoWidgetsEnableActionsPerMonth' => true,
                    'matomoWidgetsEnableVisitsPerDay' => true,
                    'matomoWidgetsEnableVisitsPerMonth' => true,
                    'matomoWidgetsEnableBounceRate' => true,
                    'matomoWidgetsEnableBrowsers' => true,
                    'matomoWidgetsEnableOsFamilies' => true,
                    'matomoWidgetsEnableCampaigns' => true,
                    'matomoWidgetsEnableCountries' => true,
                    'matomoWidgetsEnableLinkMatomo' => true,
                ]
            );

        self::assertTrue($this->subject->executeUpdate());

        GeneralUtility::purgeInstances();
    }

    /**
     * @test
     */
    public function updateNecessaryReturnsFalseIfSiteConfigurationIsAlreadyAvailable(): void
    {
        $this->configurationFinderMock
            ->expects(self::once())
            ->method('count')
            ->willReturn(1);

        $this->siteFinderMock
            ->expects(self::never())
            ->method('getAllSites');

        self::assertFalse($this->subject->updateNecessary());
    }

    /**
     * @test
     */
    public function updateNecessaryReturnsFalseIfMoreThanOneSiteIsAvailable(): void
    {
        $this->configurationFinderMock
            ->expects(self::once())
            ->method('count')
            ->willReturn(0);

        $this->siteFinderMock
            ->expects(self::once())
            ->method('getAllSites')
            ->willReturn([
                new Site('some_site', 1, []),
                new Site('another_site', 2, []),
            ]);

        $this->extensionConfigurationMock
            ->expects(self::never())
            ->method('get');

        self::assertFalse($this->subject->updateNecessary());
    }

    /**
     * @test
     */
    public function updateNecessaryReturnsTrueIfExtensionConfigurationIsAvailable(): void
    {
        $this->configurationFinderMock
            ->expects(self::once())
            ->method('count')
            ->willReturn(0);

        $this->siteFinderMock
            ->expects(self::once())
            ->method('getAllSites')
            ->willReturn([new Site('some_site', 1, [])]);

        $this->extensionConfigurationMock
            ->expects(self::once())
            ->method('get')
            ->with('matomo_widgets')
            ->willReturn([
                'idSite' => '1',
                'url' => 'https://example.org/'
            ]);

        self::assertTrue($this->subject->updateNecessary());
    }

    /**
     * @test
     */
    public function updateNecessaryReturnsFalseIfExtensionConfigurationIsNotAvailable(): void
    {
        $this->configurationFinderMock
            ->expects(self::once())
            ->method('count')
            ->willReturn(0);

        $this->siteFinderMock
            ->expects(self::once())
            ->method('getAllSites')
            ->willReturn([new Site('some_site', 1, [])]);

        $this->extensionConfigurationMock
            ->expects(self::once())
            ->method('get')
            ->with('matomo_widgets')
            ->willReturn([]);

        self::assertFalse($this->subject->updateNecessary());
    }

    /**
     * @test
     */
    public function noPrerequisitesAreNeeded(): void
    {
        self::assertSame([], $this->subject->getPrerequisites());
    }
}
