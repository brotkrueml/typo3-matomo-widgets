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
use Brotkrueml\MatomoWidgets\Extension;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

class Actions implements ChartDataProviderInterface
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

    public function __construct(
        FrontendInterface $cache,
        LanguageService $languageService,
        VisitsSummaryRepository $visitsSummaryRepository,
        string $period,
        string $date
    ) {
        $this->cache = $cache;
        $this->languageService = $languageService;
        $this->visitsSummaryRepository = $visitsSummaryRepository;
        $this->period = $period;
        $this->date = $date;
    }

    public function getChartData(): array
    {
        $cacheIdentifier = 'VisitsSummaryActions' . \ucfirst($this->period);
        $chartData = $this->cache->get($cacheIdentifier);
        if (false === $chartData) {
            $data = $this->visitsSummaryRepository->getActions($this->period, $this->date);
            $chartData = [
                'labels' => \array_keys($data),
                'datasets' => [
                    [
                        'label' => $this->languageService->sL(Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.actions.dataset.label'),
                        'backgroundColor' => WidgetApi::getDefaultChartColors()[3],
                        'data' => \array_values($data),
                    ],
                ],
            ];

            $this->cache->set($cacheIdentifier, $chartData);
        }

        return $chartData;
    }
}
