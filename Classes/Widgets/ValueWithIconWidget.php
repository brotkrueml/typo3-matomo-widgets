<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Widgets;

use Brotkrueml\MatomoWidgets\Widgets\Provider\ValueDataProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class ValueWithIconWidget implements WidgetInterface
{
    use WidgetTitleAdaptionTrait;

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
     * @var ValueDataProviderInterface
     */
    private $dataProvider;

    public function __construct(
        WidgetConfigurationInterface $configuration,
        ValueDataProviderInterface $dataProvider,
        StandaloneView $view,
        array $options = []
    ) {
        $this->configuration = $this->prefixWithSiteTitle($configuration, $options);
        $this->view = $view;
        $this->options = $options;
        $this->dataProvider = $dataProvider;
    }

    public function renderWidgetContent(): string
    {
        /** @psalm-suppress InternalMethod */
        $this->view->setTemplate('Widget/ValueWithIconWidget');
        $this->view->assignMultiple([
            'icon' => $this->options['icon'],
            'title' => $this->options['title'],
            'subtitle' => $this->options['subtitle'],
            'value' => $this->dataProvider->getValue(),
            'options' => $this->options,
            'configuration' => $this->configuration,
        ]);

        return $this->view->render();
    }
}
