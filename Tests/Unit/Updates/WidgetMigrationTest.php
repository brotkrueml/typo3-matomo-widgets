<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Updates;

use Brotkrueml\MatomoWidgets\Configuration\Configuration;
use Brotkrueml\MatomoWidgets\Configuration\ConfigurationFinder;
use Brotkrueml\MatomoWidgets\Domain\Repository\DashboardRepository;
use Brotkrueml\MatomoWidgets\Updates\WidgetMigration;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

class WidgetMigrationTest extends TestCase
{
    /** @var ConfigurationFinder|MockObject */
    private $configurationFinderMock;

    /** @var DashboardRepository|MockObject */
    private $dashboardRepositoryMock;

    /** @var MockObject|Registry */
    private $registryMock;

    /** @var WidgetMigration */
    private $subject;

    protected function setUp(): void
    {
        $this->configurationFinderMock = $this->createMock(ConfigurationFinder::class);
        $this->dashboardRepositoryMock = $this->createMock(DashboardRepository::class);
        $this->registryMock = $this->createMock(Registry::class);
        $outputDummy = $this->createStub(OutputInterface::class);
        $outputDummy->method('writeln');

        $this->subject = new WidgetMigration(
            $this->configurationFinderMock,
            $this->dashboardRepositoryMock,
            $this->registryMock
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
    public function executeUpdateMigratesMatomoWidgetsCorrectly(): void
    {
        $this->configurationFinderMock
            ->expects(self::once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([new Configuration('some_site', '', '', 1, '', [])]));

        $this->dashboardRepositoryMock
            ->expects(self::once())
            ->method('findAll')
            ->willReturnCallback(static function (): \Generator {
                yield [
                    'identifier' => '880a43aeaa67152bdfdae000f3cffec15fba5416',
                    'widgets' => \json_encode([
                        '426a0bb93c1423f1edfc9f0d8ef33a37bc1d717a' => ['identifier' => 'matomo_widgets.some_site.visitsSummary.visitsPerDay'],
                        'b37a7300537a585fa4d435c4cdd8968db315b12e' => ['identifier' => 'matomo_widgets.visitsSummary.visitsPerMonth'],
                        '26b7bd03258ea8cbd70e1deb5b7041a6f820d368' => ['identifier' => 'something.else'],
                    ]),
                ];
            });
        $this->dashboardRepositoryMock
            ->expects(self::once())
            ->method('updateWidgetConfig')
            ->with(
                '880a43aeaa67152bdfdae000f3cffec15fba5416',
                [
                    '426a0bb93c1423f1edfc9f0d8ef33a37bc1d717a' => ['identifier' => 'matomo_widgets.some_site.visitsSummary.visitsPerDay'],
                    'b37a7300537a585fa4d435c4cdd8968db315b12e' => ['identifier' => 'matomo_widgets.some_site.visitsSummary.visitsPerMonth'],
                    '26b7bd03258ea8cbd70e1deb5b7041a6f820d368' => ['identifier' => 'something.else'],
                ]
            );

        $this->subject->executeUpdate();
    }

    /**
     * @test
     */
    public function updateNecessaryReturnsFalseWhenMoreThanOneSiteConfigurationIsAvailable(): void
    {
        $this->configurationFinderMock
            ->expects(self::once())
            ->method('count')
            ->willReturn(2);

        self::assertFalse($this->subject->updateNecessary());
    }

    /**
     * @test
     */
    public function updateNecessaryReturnsFalseIfSiteConfigurationMigrationHasNotMigratedFromExtensionConfiguration(): void
    {
        $this->configurationFinderMock
            ->expects(self::once())
            ->method('count')
            ->willReturn(1);

        $this->registryMock
            ->expects(self::once())
            ->method('get')
            ->with('tx_matomo_widgets', 'matomoWidgetsSiteConfigurationMigration')
            ->willReturn(null);

        self::assertFalse($this->subject->updateNecessary());
    }

    /**
     * @test
     */
    public function updateNecessaryReturnsTrueIfRequirementsAreFulfilled(): void
    {
        $this->configurationFinderMock
            ->expects(self::once())
            ->method('count')
            ->willReturn(1);

        $this->registryMock
            ->expects(self::once())
            ->method('get')
            ->with('tx_matomo_widgets', 'matomoWidgetsSiteConfigurationMigration')
            ->willReturn(1);

        self::assertTrue($this->subject->updateNecessary());
    }

    /**
     * @test
     */
    public function getPrerequisitesReturnsKeyOfSiteConfigurationMigration(): void
    {
        self::assertSame(['matomoWidgetsSiteConfigurationMigration'], $this->subject->getPrerequisites());
    }
}
