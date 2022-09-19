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

    private readonly WidgetConfigurationInterface $configuration;

    /**
     * @param array<string, string> $options
     */
    public function __construct(
        WidgetConfigurationInterface $configuration,
        private readonly TableDataProviderInterface $dataProvider,
        private readonly StandaloneView $view,
        private readonly ?ButtonProviderInterface $buttonProvider = null,
        private readonly array $options = []
    ) {
        $this->configuration = $this->prefixWithSiteTitle($configuration, $options);
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
