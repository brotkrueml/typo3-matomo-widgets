<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Widgets;

use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface;
use TYPO3\CMS\Dashboard\Widgets\ButtonProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\EventDataInterface;
use TYPO3\CMS\Dashboard\Widgets\JavaScriptInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * @internal
 */
final class BarChartWidget implements WidgetInterface, EventDataInterface, AdditionalCssInterface, JavaScriptInterface
{
    use WidgetTitleAdaptionTrait;

    private readonly WidgetConfigurationInterface $configuration;

    /**
     * @param array<string, string> $options
     */
    public function __construct(
        WidgetConfigurationInterface $configuration,
        private readonly ChartDataProviderInterface $dataProvider,
        private readonly StandaloneView $view,
        private readonly ?ButtonProviderInterface $buttonProvider = null,
        private readonly array $options = [],
    ) {
        $this->configuration = $this->prefixWithSiteTitle($configuration, $options);
    }

    public function renderWidgetContent(): string
    {
        $this->view->setTemplate('Widget/ChartWidget');
        $this->view->assignMultiple([
            'button' => $this->buttonProvider,
            'options' => $this->options,
            'reportLink' => $this->options['reportLink'] ?? '',
            'configuration' => $this->configuration,
        ]);
        return $this->view->render();
    }

    /**
     * @return array{graphConfig: array<string, mixed>}
     */
    public function getEventData(): array
    {
        return [
            'graphConfig' => [
                'type' => 'bar',
                'options' => [
                    'maintainAspectRatio' => false,
                    'legend' => [
                        'display' => false,
                    ],
                    'scales' => [
                        'yAxes' => [
                            [
                                'ticks' => [
                                    'beginAtZero' => true,
                                ],
                            ],
                        ],
                        'xAxes' => [
                            [
                                'ticks' => [
                                    'maxTicksLimit' => 15,
                                ],
                            ],
                        ],
                    ],
                ],
                'data' => $this->dataProvider->getChartData(),
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function getCssFiles(): array
    {
        return [
            'EXT:dashboard/Resources/Public/Css/Contrib/chart.css',
        ];
    }

    /**
     * @return JavaScriptModuleInstruction[]
     */
    public function getJavaScriptModuleInstructions(): array
    {
        return [
            JavaScriptModuleInstruction::forRequireJS('TYPO3/CMS/Dashboard/Contrib/chartjs'),
            JavaScriptModuleInstruction::forRequireJS('TYPO3/CMS/Dashboard/ChartInitializer'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
