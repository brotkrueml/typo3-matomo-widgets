<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Widgets;

use Brotkrueml\MatomoWidgets\Extension;
use Brotkrueml\MatomoWidgets\Widgets\Provider\TableDataProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface;
use TYPO3\CMS\Dashboard\Widgets\ButtonProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * @internal
 */
class TableWidget implements WidgetInterface, AdditionalCssInterface
{
    use WidgetTitleAdaptionTrait;

    private WidgetConfigurationInterface $configuration;
    private TableDataProviderInterface $dataProvider;
    private StandaloneView $view;
    private ?ButtonProviderInterface $buttonProvider;
    /**
     * @var array<string, string>
     */
    private array $options;

    /**
     * @param array<string, string> $options
     */
    public function __construct(
        WidgetConfigurationInterface $configuration,
        TableDataProviderInterface $dataProvider,
        StandaloneView $view,
        ?ButtonProviderInterface $buttonProvider = null,
        array $options = []
    ) {
        $this->configuration = $this->prefixWithSiteTitle($configuration, $options);
        $this->dataProvider = $dataProvider;
        $this->view = $view;
        $this->buttonProvider = $buttonProvider;
        $this->options = $options;
    }

    public function renderWidgetContent(): string
    {
        $this->view->setTemplate('Widget/TableWidget.html');
        $this->view->assignMultiple([
            'table' => [
                'classes' => $this->dataProvider->getClasses(),
                'columns' => $this->dataProvider->getColumns(),
                'decorators' => $this->dataProvider->getDecorators(),
                'headers' => $this->dataProvider->getHeaders(),
                'rows' => $this->dataProvider->getRows(),
            ],
            'button' => $this->buttonProvider,
            'configuration' => $this->configuration,
            'reportLink' => $this->options['reportLink'] ?? '',
        ]);

        return $this->view->render();
    }

    /**
     * @return array<string, string>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return list<string>
     */
    public function getCssFiles(): array
    {
        return [
            'EXT:' . Extension::KEY . '/Resources/Public/Css/matomo-widgets.css',
        ];
    }
}
