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

/**
 * @internal
 */
final class GenericBarChartDataProvider implements ChartDataProviderInterface
{
    private MatomoRepositoryInterface $repository;
    private ConnectionConfiguration $connectionConfiguration;
    private LanguageService $languageService;
    private string $method;
    private string $barLabel;
    private string $backgroundColour;
    /**
     * @var array<string, string>
     */
    private array $parameters;

    /**
     * @param array<string, string> $parameters
     */
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

    /**
     * @return array<mixed>
     */
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
