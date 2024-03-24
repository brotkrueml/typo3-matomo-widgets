<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Parameter;

use Brotkrueml\MatomoWidgets\Extension;
use TYPO3\CMS\Core\Localization\LanguageService;

/**
 * @internal
 */
final class PeriodResolver implements PeriodResolverInterface
{
    /**
     * @see https://regex101.com/r/RnHVMW/1
     */
    private const DATE_LAST_REGEX = '/^last(\d+)$/';

    /**
     * @see https://regex101.com/r/RicLVZ/1
     */
    private const DATE_PREVIOUS_REGEX = '/^previous(\d+)$/';

    /**
     * @see https://regex101.com/r/JbtaKc/1
     */
    private const DATE_RANGE_REGEX = '/^(\d{4}-\d{2}-\d{2}),(\d{4}-\d{2}-\d{2})$/';

    public function resolve(string $period, string $date): string
    {
        return match ($period) {
            'range' => $this->handleRangePeriod($date),
            default => $this->handleUnsupportedPeriod($period, $date),
        };
    }

    private function handleRangePeriod(string $date): string
    {
        if (\preg_match(self::DATE_LAST_REGEX, $date, $matches)) {
            return \sprintf($this->translate('period.range.last'), $matches[1]);
        }

        if (\preg_match(self::DATE_PREVIOUS_REGEX, $date, $matches)) {
            return \sprintf($this->translate('period.range.previous'), $matches[1]);
        }

        if (\preg_match(self::DATE_RANGE_REGEX, $date, $matches)) {
            return \sprintf($this->translate('period.range.range'), $matches[1], $matches[2]);
        }

        // Date formats not handled above return the configuration
        return 'Range: ' . $date;
    }

    private function handleUnsupportedPeriod(string $period, string $date): string
    {
        // These are currently unsupported, return the configuration.
        // If required, they can be implemented later.
        return \ucfirst($period) . ': ' . $date;
    }

    private function translate(string $key): string
    {
        return $this->getLanguageService()->sL(Extension::LANGUAGE_PATH_DASHBOARD . ':' . $key);
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
