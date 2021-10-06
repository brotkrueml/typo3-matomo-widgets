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
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

final class GenericBarChartDataProvider implements ChartDataProviderInterface
{
    /**
     * @var MatomoRepositoryInterface
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
    private $barLabel;

    /**
     * @var string
     */
    private $backgroundColour;

    /**
     * @var array
     */
    private $parameters;

    public function __construct(
        MatomoRepositoryInterface $repository,
        ConnectionConfiguration $connectionConfiguration,
        LanguageService $languageService,
        string $method,
        string $barLabel,
        string $backgroundColour,
        array $parameters
    ) {
        $this->repository = $repository;
        $this->connectionConfiguration = $connectionConfiguration;
        $this->languageService = $languageService;
        $this->method = $method;
        $this->backgroundColour = $backgroundColour;
        $this->barLabel = $barLabel;
        $this->parameters = $parameters;
    }

    public function getChartData(): array
    {
        $data = $this->repository->send($this->connectionConfiguration, $this->method, new ParameterBag($this->parameters));

        return [
            'labels' => \array_keys($data),
            'datasets' => [
                [
                    'label' => $this->languageService->sL($this->barLabel),
                    'backgroundColor' => $this->backgroundColour,
                    'data' => \array_values($data),
                ],
            ],
        ];
    }
}
