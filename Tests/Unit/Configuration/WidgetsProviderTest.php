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
use PHPUnit\Framework\TestCase;

class WidgetsProviderTest extends TestCase
{
    /** @var WidgetsProvider */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new WidgetsProvider();
    }

    /**
     * @test
     */
    public function getWidgetConfigurationKeysReturnsCorrectKeys(): void
    {
        $actual = $this->subject->getWidgetConfigurationKeys();
        $expected = [
            'matomoWidgetsEnableActionsPerDay',
            'matomoWidgetsEnableActionsPerMonth',
            'matomoWidgetsEnableVisitsPerDay',
            'matomoWidgetsEnableVisitsPerMonth',
            'matomoWidgetsEnableBounceRate',
            'matomoWidgetsEnableBrowsers',
            'matomoWidgetsEnableOsFamilies',
            'matomoWidgetsEnableCampaigns',
            'matomoWidgetsEnableCountries',
            'matomoWidgetsEnableLinkMatomo',
        ];

        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getTitleForWidgetReturnsTheTitle(): void
    {
        $actual = $this->subject->getTitleForWidget('matomoWidgetsEnableActionsPerDay');

        self::assertSame(
            'LLL:EXT:matomo_widgets/Resources/Private/Language/Dashboard.xlf:widgets.visitsSummary.actionsPerDay.title',
            $actual
        );
    }
}
