<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Widgets\Provider\DevicesDetection;

use Brotkrueml\MatomoWidgets\Domain\Repository\DevicesDetectionRepository;
use Brotkrueml\MatomoWidgets\Extension;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

class Browsers implements ChartDataProviderInterface
{
    /** @var FrontendInterface */
    private $cache;

    /** @var LanguageService */
    private $languageService;

    /** @var DevicesDetectionRepository */
    private $devicesDetectionRepository;

    /** @var string */
    private $period;

    /** @var string */
    private $date;

    /** @var int */
    private $maxItemsBeforeCombineOthers;

    public function __construct(
        FrontendInterface $cache,
        LanguageService $languageService,
        DevicesDetectionRepository $devicesDetectionRepository,
        string $period,
        string $date,
        int $maxItemsBeforeCombineOthers
    ) {
        $this->cache = $cache;
        $this->languageService = $languageService;
        $this->devicesDetectionRepository = $devicesDetectionRepository;
        $this->period = $period;
        $this->date = $date;
        $this->maxItemsBeforeCombineOthers = $maxItemsBeforeCombineOthers;
    }

    public function getChartData(): array
    {
        $cacheIdentifier = 'DevicesDetectionBrowsers';
        $chartData = $this->cache->get($cacheIdentifier);
        if (false === $chartData) {
            $browsers = $this->getBrowsers();
            $chartData = [
                'labels' => \array_keys($browsers),
                'datasets' => [
                    [
                        'backgroundColor' => WidgetApi::getDefaultChartColors(),
                        'data' => \array_values($browsers),
                    ],
                ],
            ];

            $this->cache->set($cacheIdentifier, $chartData);
        }

        return $chartData;
    }

    private function getBrowsers(): array
    {
        $data = $this->devicesDetectionRepository->getBrowsers($this->period, $this->date);

        $browsers = [];
        $otherBrowsers = 0;
        foreach ($data as $browser) {
            if (\count($browsers) < $this->maxItemsBeforeCombineOthers) {
                $browsers[$browser['label']] = $browser['nb_visits'];
                continue;
            }

            $otherBrowsers += $browser['nb_visits'];
        }

        if ($otherBrowsers) {
            $browsers[$this->languageService->sL(Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.devicesDetection.browsers.other')] = $otherBrowsers;
        }

        return $browsers;
    }
}
