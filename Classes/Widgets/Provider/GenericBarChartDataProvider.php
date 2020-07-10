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
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

final class GenericBarChartDataProvider implements ChartDataProviderInterface
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
    private $barLabel;

    /**
     * @var string
     */
    private $barColour;

    /**
     * @var array
     */
    private $parameters;

    public function __construct(
        RepositoryInterface $repository,
        LanguageService $languageService,
        string $method,
        string $barLabel,
        string $barColour,
        array $parameters
    ) {
        $this->repository = $repository;
        $this->languageService = $languageService;
        $this->method = $method;
        $this->barColour = $barColour;
        $this->barLabel = $barLabel;
        $this->parameters = $parameters;
    }

    public function getChartData(): array
    {
        $data = $this->repository->find($this->method, new ParameterBag($this->parameters));

        return [
            'labels' => \array_keys($data),
            'datasets' => [
                [
                    'label' => $this->languageService->sL($this->barLabel),
                    'backgroundColor' => $this->barColour,
                    'data' => \array_values($data),
                ],
            ],
        ];
    }
}
