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
use Brotkrueml\MatomoWidgets\Domain\Repository\RepositoryInterface;
use Brotkrueml\MatomoWidgets\Extension;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

final class GenericDoughnutChartDataProvider implements ChartDataProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var ConnectionConfiguration
     */
    private $connectionConfiguration;

    /**
     * @var LanguageService
     */
    private $languageService;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $labelColumn;

    /**
     * @var string
     */
    private $valueColumn;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var list<string>
     */
    private $backgroundColours;

    /**
     * @var array<string,string>
     */
    private $parameters;

    public function __construct(
        RepositoryInterface $repository,
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

    private function aggregateData(): array
    {
        $rows = $this->repository->find($this->connectionConfiguration, $this->method, new ParameterBag($this->parameters));

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

        if ($valueOther) {
            $data[$this->languageService->sL(Extension::LANGUAGE_PATH_DASHBOARD . ':other')] = $valueOther;
        }

        return $data;
    }
}
