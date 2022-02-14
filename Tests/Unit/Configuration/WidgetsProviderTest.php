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
use PHPUnit\Framework\TestCase;

class WidgetsProviderTest extends TestCase
{
    /**
     * @var WidgetsProvider
     */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new WidgetsProvider();
    }

    /**
     * @test
     */
    public function getWidgetIdentifiers(): void
    {
        $actual = $this->subject->getWidgetIdentifiers();

        self::assertContains('actionsPerDay', $actual);
        self::assertContains('actionsPerMonth', $actual);
        self::assertContains('annotations', $actual);
        self::assertContains('createAnnotation', $actual);
        self::assertContains('bounceRate', $actual);
        self::assertContains('browsers', $actual);
        self::assertContains('campaigns', $actual);
        self::assertContains('contentNames', $actual);
        self::assertContains('contentPieces', $actual);
        self::assertContains('countries', $actual);
        self::assertContains('javaScriptErrors', $actual);
        self::assertContains('linkMatomo', $actual);
        self::assertContains('osFamilies', $actual);
        self::assertContains('pagesNotFound', $actual);
        self::assertContains('siteSearchKeywords', $actual);
        self::assertContains('siteSearchNoResultKeywords', $actual);
        self::assertContains('visitsPerDay', $actual);
        self::assertContains('visitsPerMonth', $actual);
    }

    /**
     * @test
     */
    public function getItemsForTca(): void
    {
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
}
