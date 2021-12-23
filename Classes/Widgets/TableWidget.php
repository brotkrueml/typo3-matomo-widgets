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

class TableWidget implements WidgetInterface, AdditionalCssInterface
{
    use WidgetTitleAdaptionTrait;

    /**
     * @var WidgetConfigurationInterface
     */
    private $configuration;

    /**
     * @var TableDataProviderInterface
     */
    private $dataProvider;

    /**
     * @var StandaloneView
     */
    private $view;

    /**
     * @var ButtonProviderInterface|null
     */
    private $buttonProvider;

    /**
     * @var array<string, string>
     */
    private $options;

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
     * @return list<string>
     */
    public function getCssFiles(): array
    {
        return [
            \sprintf('EXT:%s/Resources/Public/Css/matomo-widgets.css', Extension::KEY),
        ];
    }
}
