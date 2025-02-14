<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Configuration;

use Brotkrueml\MatomoWidgets\Configuration\LegacyEnableWidgetsConfigurationFinder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;

#[CoversClass(LegacyEnableWidgetsConfigurationFinder::class)]
final class LegacyEnableWidgetsConfigurationFinderTest extends TestCase
{
    #[Test]
    public function getEnableWidgetsIdentifier(): void
    {
        $siteFinderStub = self::createStub(SiteFinder::class);
        $siteFinderStub
            ->method('getAllSites')
            ->willReturn([]);

        $subject = new LegacyEnableWidgetsConfigurationFinder(
            $siteFinderStub,
        );

        $actual = $subject->getEnableWidgetsIdentifier();

        self::assertContains('matomoWidgetsEnableActionsPerDay', $actual);
        self::assertContains('matomoWidgetsEnableActionsPerMonth', $actual);
        self::assertContains('matomoWidgetsEnableBounceRate', $actual);
        self::assertContains('matomoWidgetsEnableBrowsers', $actual);
        self::assertContains('matomoWidgetsEnableCampaigns', $actual);
        self::assertContains('matomoWidgetsEnableCountries', $actual);
        self::assertContains('matomoWidgetsEnableLinkMatomo', $actual);
        self::assertContains('matomoWidgetsEnableOsFamilies', $actual);
        self::assertContains('matomoWidgetsEnableVisitsPerDay', $actual);
        self::assertContains('matomoWidgetsEnableVisitsPerMonth', $actual);
    }

    #[Test]
    public function countReturns0WhenNoConfigurationsFound(): void
    {
        $siteFinderStub = self::createStub(SiteFinder::class);
        $siteFinderStub
            ->method('getAllSites')
            ->willReturn([]);

        $subject = new LegacyEnableWidgetsConfigurationFinder(
            $siteFinderStub,
        );

        self::assertEmpty($subject);
    }

    #[Test]
    public function countReturnsCountCorrectlyWhenConfigurationsFound(): void
    {
        $siteStub1 = self::createStub(Site::class);
        $siteStub1
            ->method('getConfiguration')
            ->willReturn([
                'matomoWidgetsEnableActionsPerDay' => true,
            ]);

        $siteStub2 = self::createStub(Site::class);
        $siteStub2
            ->method('getConfiguration')
            ->willReturn([
                'matomoWidgetsEnableBounceRate' => false,
            ]);

        $siteFinderStub = self::createStub(SiteFinder::class);
        $siteFinderStub
            ->method('getAllSites')
            ->willReturn([
                'main' => $siteStub1,
                'other' => $siteStub2,
            ]);

        $subject = new LegacyEnableWidgetsConfigurationFinder(
            $siteFinderStub,
        );

        self::assertCount(2, $subject);
    }

    #[Test]
    public function getIterator(): void
    {
        $siteStub1 = self::createStub(Site::class);
        $siteStub1
            ->method('getConfiguration')
            ->willReturn([
                'matomoWidgetsEnableActionsPerDay' => true,
            ]);

        $siteStub2 = self::createStub(Site::class);
        $siteStub2
            ->method('getConfiguration')
            ->willReturn([
                'matomoWidgetsEnableBounceRate' => false,
            ]);

        $siteFinderStub = self::createStub(SiteFinder::class);
        $siteFinderStub
            ->method('getAllSites')
            ->willReturn([
                'main' => $siteStub1,
                'other' => $siteStub2,
            ]);

        $subject = new LegacyEnableWidgetsConfigurationFinder(
            $siteFinderStub,
        );

        $iterator = $subject->getIterator();
        self::assertInstanceOf(\ArrayIterator::class, $iterator);

        self::assertSame([
            'matomoWidgetsEnableActionsPerDay' => true,
        ], $iterator->current());
        $iterator->next();
        self::assertSame([
            'matomoWidgetsEnableBounceRate' => false,
        ], $iterator->current());
        $iterator->next();
        self::assertNull($iterator->current());
    }
}
