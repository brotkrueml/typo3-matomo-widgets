<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Widgets\Provider;

use Brotkrueml\MatomoWidgets\Connection\ConnectionConfiguration;
use Brotkrueml\MatomoWidgets\Domain\Repository\MatomoRepositoryInterface;
use Brotkrueml\MatomoWidgets\Extension;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

/**
 * @internal
 */
final class GenericDoughnutChartDataProvider implements ChartDataProviderInterface
{
    private MatomoRepositoryInterface $repository;
    private ConnectionConfiguration $connectionConfiguration;
    private LanguageService $languageService;
    private string $method;
    private string $labelColumn;
    private string $valueColumn;
    private int $limit;
    /**
     * @var list<string>
     */
    private array $backgroundColours;
    /**
     * @var array<string,string>
     */
    private array $parameters;

    /**
     * @param list<string> $backgroundColours
     * @param array<string, string> $parameters
     */
    public function __construct(
        MatomoRepositoryInterface $repository,
        ConnectionConfiguration $connectionConfiguration,
        LanguageService $languageService,
        string $method,
        string $labelColumn,
        string $valueColumn,
        int $limit,
        array $backgroundColours,
        array $parameters
    ) {
        $this->repository = $repository;
        $this->connectionConfiguration = $connectionConfiguration;
        $this->languageService = $languageService;
        $this->method = $method;
        $this->labelColumn = $labelColumn;
        $this->valueColumn = $valueColumn;
        $this->limit = $limit;
        $this->backgroundColours = $backgroundColours;
        $this->parameters = $parameters;
    }

    /**
     * @return array{labels: list<string>, datasets: list<mixed>}
     */
    public function getChartData(): array
    {
        $data = $this->aggregateData();

        return [
            'labels' => \array_keys($data),
            'datasets' => [
                [
                    'backgroundColor' => \array_slice($this->backgroundColours, 0, \count($data)),
                    'data' => \array_values($data),
                ],
            ],
        ];
    }

    /**
     * @return array<mixed>
     */
    private function aggregateData(): array
    {
        $rows = $this->repository->send($this->connectionConfiguration, $this->method, new ParameterBag($this->parameters));

        $data = [];
        $valueOther = 0;
        foreach ($rows as $row) {
            $value = (int)$row[$this->valueColumn];
            if (\count($data) < $this->limit) {
                $data[$row[$this->labelColumn]] = $value;
                continue;
            }

            $valueOther += $value;
        }

        if ($valueOther !== 0) {
            $data[$this->languageService->sL(Extension::LANGUAGE_PATH_DASHBOARD . ':other')] = $valueOther;
        }

        return $data;
    }
}
