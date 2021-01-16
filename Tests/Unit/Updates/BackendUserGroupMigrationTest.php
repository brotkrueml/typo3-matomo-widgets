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
use Brotkrueml\MatomoWidgets\Domain\Repository\BackendUserGroupRepository;
use Brotkrueml\MatomoWidgets\Updates\BackendUserGroupMigration;
use Brotkrueml\MatomoWidgets\Updates\WidgetMigration;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

class BackendUserGroupMigrationTest extends TestCase
{
    /** @var ConfigurationFinder|MockObject */
    private $configurationFinderMock;

    /** @var BackendUserGroupRepository|MockObject */
    private $backendUserGroupRepositoryMock;

    /** @var WidgetMigration */
    private $subject;

    protected function setUp(): void
    {
        $this->configurationFinderMock = $this->createMock(ConfigurationFinder::class);
        $this->backendUserGroupRepositoryMock = $this->createMock(BackendUserGroupRepository::class);
        $outputDummy = $this->createStub(OutputInterface::class);
        $outputDummy->method('writeln');

        $this->subject = new BackendUserGroupMigration($this->configurationFinderMock, $this->backendUserGroupRepositoryMock);
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

        $this->backendUserGroupRepositoryMock
            ->expects(self::once())
            ->method('findAll')
            ->willReturnCallback(static function (): \Generator {
                yield [
                    'uid' => 42,
                    'availableWidgets' => 't3news,matomo_widgets.visitsSummary.actionsPerDay,matomo_widgets.visitsSummary.actionsPerMonth,matomo_widgets.some_site.visitsSummary.bounceRate',
                ];
            });
        $this->backendUserGroupRepositoryMock
            ->expects(self::once())
            ->method('updateAvailableWidgets')
            ->with(
                42,
                [
                    't3news',
                    'matomo_widgets.some_site.visitsSummary.actionsPerDay',
                    'matomo_widgets.some_site.visitsSummary.actionsPerMonth',
                    'matomo_widgets.some_site.visitsSummary.bounceRate',
                ]
            );

        $this->subject->executeUpdate();
    }

    /**
     * @test
     */
    public function updateNecessaryReturnsFalseWhenNoSiteConfigurationIsAvailable(): void
    {
        $this->configurationFinderMock
            ->expects(self::once())
            ->method('count')
            ->willReturn(0);

        self::assertFalse($this->subject->updateNecessary());
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
    public function updateNecessaryReturnsTrueIfOnlyOneSiteConfigurationIsDefined(): void
    {
        $this->configurationFinderMock
            ->expects(self::once())
            ->method('count')
            ->willReturn(1);

        self::assertTrue($this->subject->updateNecessary());
    }

    /**
     * @test
     */
    public function noPrerequisitesAreNeeded(): void
    {
        self::assertSame([], $this->subject->getPrerequisites());
    }
}
