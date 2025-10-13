<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Configuration;

use Brotkrueml\MatomoWidgets\Configuration\Widgets;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Widgets::class)]
final class WidgetsTest extends TestCase
{
    #[Test]
    public function getItemsForSiteConfiguration(): void
    {
        $actual = Widgets::getItemsForSiteConfiguration();

        self::assertSame(
            'LLL:EXT:matomo_widgets/Resources/Private/Language/Dashboard.xlf:widgets.visitsSummary.actionsPerDay.title',
            $actual[0]['label'],
            'The label is correctly built',
        );
        self::assertSame(
            'actionsPerDay',
            $actual[0]['value'],
            'The value is in lowerCamelCase',
        );
    }
}
