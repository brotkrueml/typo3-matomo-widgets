<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Widgets;

use TYPO3\CMS\Dashboard\Widgets\ButtonProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * @internal
 */
final class CtaWidget implements WidgetInterface
{
    use WidgetTitleAdaptionTrait;

    private WidgetConfigurationInterface $configuration;
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
        StandaloneView $view,
        ?ButtonProviderInterface $buttonProvider = null,
        array $options = []
    ) {
        $this->configuration = $this->prefixWithSiteTitle($configuration, $options);
        $this->view = $view;
        $this->buttonProvider = $buttonProvider;
        $this->options = \array_merge([
            'text' => '',
        ], $options);
    }

    public function renderWidgetContent(): string
    {
        $this->view->setTemplate('Widget/CtaWidget');
        $this->view->assignMultiple([
            'text' => $this->options['text'],
            'options' => $this->options,
            'button' => $this->buttonProvider,
            'configuration' => $this->configuration,
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
}
