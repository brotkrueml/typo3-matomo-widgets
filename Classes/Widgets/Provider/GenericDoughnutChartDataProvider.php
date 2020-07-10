<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Widgets\Provider;

use Brotkrueml\MatomoWidgets\Domain\Repository\RepositoryInterface;
use Brotkrueml\MatomoWidgets\Extension;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

final class GenericDoughnutChartDataProvider implements ChartDataProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

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
     * @var array<string,string>
     */
    private $parameters;

    public function __construct(
        RepositoryInterface $repository,
        LanguageService $languageService,
        string $method,
        string $labelColumn,
        string $valueColumn,
        int $limit,
        array $parameters
    ) {
        $this->repository = $repository;
        $this->languageService = $languageService;
        $this->method = $method;
        $this->labelColumn = $labelColumn;
        $this->valueColumn = $valueColumn;
        $this->limit = $limit;
        $this->parameters = $parameters;
    }

    public function getChartData(): array
    {
        $data = $this->getMatomoData();

        return [
            'labels' => \array_keys($data),
            'datasets' => [
                [
                    'backgroundColor' => WidgetApi::getDefaultChartColors(),
                    'data' => \array_values($data),
                ],
            ],
        ];
    }

    private function getMatomoData(): array
    {
        $parameterBag = new ParameterBag();
        foreach ($this->parameters as $name => $value) {
            $parameterBag->set($name, $value);
        }

        $rows = $this->repository->find($this->method, $parameterBag);

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
