<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Configuration;

use Brotkrueml\MatomoWidgets\Configuration\WidgetsProvider;
use Brotkrueml\MatomoWidgets\Extension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Information\Typo3Version;

#[CoversClass(WidgetsProvider::class)]
final class WidgetsProviderTest extends TestCase
{
    private WidgetsProvider $subject;

    protected function setUp(): void
    {
        $this->subject = new WidgetsProvider();
    }

    #[Test]
    public function getItemsForTcaInV11(): void
    {
        if ((new Typo3Version())->getMajorVersion() > 11) {
            self::markTestSkipped('Only relevant for TYPO3 v11');
        }

        $actual = $this->subject->getItemsForTca();

        self::assertContains([Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.actionsPerDay.title', 'actionsPerDay'], $actual);
        self::assertContains([Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.actionsPerMonth.title', 'actionsPerMonth'], $actual);
        self::assertContains([Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.annotations.title', 'annotations'], $actual);
        self::assertContains([Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.createAnnotation.title', 'createAnnotation'], $actual);
        self::assertContains([Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.bounceRate.title', 'bounceRate'], $actual);
        self::assertContains([Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.referrers.campaigns.title', 'campaigns'], $actual);
        self::assertContains([Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.contents.contentNames.title', 'contentNames'], $actual);
        self::assertContains([Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.contents.contentPieces.title', 'contentPieces'], $actual);
        self::assertContains([Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.userCountry.country.title', 'countries'], $actual);
        self::assertContains([Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.events.javaScriptErrors.title', 'javaScriptErrors'], $actual);
        self::assertContains([Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.linkMatomo.title', 'linkMatomo'], $actual);
        self::assertContains([Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.devicesDetection.osFamilies.title', 'osFamilies'], $actual);
        self::assertContains([Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.actions.pagesNotFound.title', 'pagesNotFound'], $actual);
        self::assertContains([Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.actions.siteSearchKeywords.title', 'siteSearchKeywords'], $actual);
        self::assertContains([Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.actions.siteSearchNoResultKeywords.title', 'siteSearchNoResultKeywords'], $actual);
        self::assertContains([Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.visitsPerDay.title', 'visitsPerDay'], $actual);
        self::assertContains([Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.visitsPerMonth.title', 'visitsPerMonth'], $actual);
    }

    #[Test]
    public function getItemsForTca(): void
    {
        if ((new Typo3Version())->getMajorVersion() < 12) {
            self::markTestSkipped('Only relevant for TYPO3 v12+');
        }

        $actual = $this->subject->getItemsForTca();

        self::assertContains([
            'label' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.actionsPerDay.title',
            'value' => 'actionsPerDay',
        ], $actual);
        self::assertContains([
            'label' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.actionsPerMonth.title',
            'value' => 'actionsPerMonth',
        ], $actual);
        self::assertContains([
            'label' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.annotations.title',
            'value' => 'annotations',
        ], $actual);
        self::assertContains([
            'label' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.createAnnotation.title',
            'value' => 'createAnnotation',
        ], $actual);
        self::assertContains([
            'label' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.bounceRate.title',
            'value' => 'bounceRate',
        ], $actual);
        self::assertContains([
            'label' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.referrers.campaigns.title',
            'value' => 'campaigns',
        ], $actual);
        self::assertContains([
            'label' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.contents.contentNames.title',
            'value' => 'contentNames',
        ], $actual);
        self::assertContains([
            'label' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.contents.contentPieces.title',
            'value' => 'contentPieces',
        ], $actual);
        self::assertContains([
            'label' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.userCountry.country.title',
            'value' => 'countries',
        ], $actual);
        self::assertContains([
            'label' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.events.javaScriptErrors.title',
            'value' => 'javaScriptErrors',
        ], $actual);
        self::assertContains([
            'label' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.linkMatomo.title',
            'value' => 'linkMatomo',
        ], $actual);
        self::assertContains([
            'label' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.devicesDetection.osFamilies.title',
            'value' => 'osFamilies',
        ], $actual);
        self::assertContains([
            'label' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.actions.pagesNotFound.title',
            'value' => 'pagesNotFound',
        ], $actual);
        self::assertContains([
            'label' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.actions.siteSearchKeywords.title',
            'value' => 'siteSearchKeywords',
        ], $actual);
        self::assertContains([
            'label' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.actions.siteSearchNoResultKeywords.title',
            'value' => 'siteSearchNoResultKeywords',
        ], $actual);
        self::assertContains([
            'label' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.visitsPerDay.title',
            'value' => 'visitsPerDay',
        ], $actual);
        self::assertContains([
            'label' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.visitsPerMonth.title',
            'value' => 'visitsPerMonth',
        ], $actual);
    }
}
