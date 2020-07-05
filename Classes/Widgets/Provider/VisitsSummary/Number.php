<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Widgets\Provider\VisitsSummary;

use Brotkrueml\MatomoWidgets\Domain\Repository\VisitsSummaryRepository;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\Widgets\NumberWithIconDataProviderInterface;

class Number implements NumberWithIconDataProviderInterface
{
    /** @var FrontendInterface */
    private $cache;

    /** @var LanguageService */
    private $languageService;

    /** @var VisitsSummaryRepository */
    private $visitsSummaryRepository;

    /** @var string */
    private $period;

    /** @var string */
    private $date;

    /** @var string */
    private $column;

    public function __construct(
        FrontendInterface $cache,
        LanguageService $languageService,
        VisitsSummaryRepository $visitsSummaryRepository,
        string $period,
        string $date,
        string $column
    ) {
        $this->cache = $cache;
        $this->languageService = $languageService;
        $this->visitsSummaryRepository = $visitsSummaryRepository;
        $this->period = $period;
        $this->date = $date;
        $this->column = $column;
    }

    public function getNumber(): int
    {
        $cacheIdentifier = \sprintf(
            'VisitsSummary%s%s',
            GeneralUtility::underscoredToUpperCamelCase($this->column),
            \ucfirst($this->period)
        );
        $number = $this->cache->get($cacheIdentifier);
        if (false === $number) {
            $number = (int)$this->visitsSummaryRepository->get($this->period, $this->date, $this->column)['value'];
            $this->cache->set($cacheIdentifier, $number);
        }

        return $number;
    }
}
