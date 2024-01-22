<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\DateTime;

use Psr\Clock\ClockInterface;

/**
 * Resolves periods defined in the widget configuration, for example, period=month and date=today
 * to the date range.
 *
 * @see https://developer.matomo.org/api-reference/reporting-api#standard-api-parameters
 * @internal
 */
final class MatomoPeriodResolver
{
    // @todo consider period=range
    // @todo consider date=lastWeek|lastMonth|lastYear|lastX|previousX
    public function toDateRange(string $period, string $date): DateRange
    {
        $endDate = new \DateTimeImmutable($date);

        $startDate = match ($period) {
            'day' => $endDate->setTime(0, 0, 0),
            'week' => $endDate->modify('monday this week'),
            'month' => $endDate->modify('first day of this month'),
            'year' => $endDate->modify('first day of this year'),
        };

        return new DateRange($startDate, $endDate);
    }
}
