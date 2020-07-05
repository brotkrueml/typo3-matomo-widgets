<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Widgets;

use TYPO3\CMS\Dashboard\Widgets\NumberWithIconDataProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class PercentNumberWithIconWidget implements WidgetInterface
{
    /**
     * @var WidgetConfigurationInterface
     */
    private $configuration;
    /**
     * @var StandaloneView
     */
    private $view;
    /**
     * @var array
     */
    private $options;
    /**
     * @var NumberWithIconDataProviderInterface
     */
    private $dataProvider;

    public function __construct(
        WidgetConfigurationInterface $configuration,
        NumberWithIconDataProviderInterface $dataProvider,
        StandaloneView $view,
        array $options = []
    ) {
        $this->configuration = $configuration;
        $this->view = $view;
        $this->options = $options;
        $this->dataProvider = $dataProvider;
    }

    /**
     * @inheritDoc
     */
    public function renderWidgetContent(): string
    {
        $this->view->setTemplate('Widget/NumberWithIconWidget');
        $this->view->assignMultiple([
            'icon' => $this->options['icon'],
            'title' => $this->options['title'],
            'subtitle' => $this->options['subtitle'],
            'number' => $this->dataProvider->getNumber() . '%',
            'options' => $this->options,
            'configuration' => $this->configuration
        ]);

        return $this->view->render();
    }
}
