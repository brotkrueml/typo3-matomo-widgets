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
use Brotkrueml\MatomoWidgets\Domain\Repository\MatomoRepository;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

/**
 * @internal
 */
final class GenericBarChartDataProvider implements ChartDataProviderInterface
{
    /**
     * @param array<string, string> $parameters
     */
    public function __construct(
        private readonly MatomoRepository $repository,
        private readonly ConnectionConfiguration $connectionConfiguration,
        private readonly string $method,
        private readonly string $barLabel,
        private readonly string $backgroundColour,
        private readonly array $parameters,
    ) {
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
                    'label' => $this->getLanguageService()->sL($this->barLabel),
                    'backgroundColor' => $this->backgroundColour,
                    'data' => \array_values($data),
                ],
            ],
        ];
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
