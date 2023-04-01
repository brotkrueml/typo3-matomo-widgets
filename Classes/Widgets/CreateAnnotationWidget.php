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
use TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface;
use TYPO3\CMS\Dashboard\Widgets\AdditionalJavaScriptInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * @internal
 */
final class CreateAnnotationWidget implements WidgetInterface, AdditionalCssInterface, AdditionalJavaScriptInterface
{
    use WidgetTitleAdaptionTrait;

    private readonly WidgetConfigurationInterface $configuration;

    /**
     * @param array<string, string> $options
     */
    public function __construct(
        WidgetConfigurationInterface $configuration,
        private readonly StandaloneView $view,
        private readonly array $options = [],
    ) {
        $this->configuration = $this->prefixWithSiteTitle($configuration, $options);
    }

    public function renderWidgetContent(): string
    {
        $this->view->setTemplate('Widget/CreateAnnotationWidget.html');
        $this->view->assignMultiple([
            'configuration' => $this->configuration,
            'reportLink' => $this->options['reportLink'] ?? '',
            'siteIdentifier' => $this->options['siteIdentifier'] ?? '',
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

    /**
     * @return list<string>
     */
    public function getJsFiles(): array
    {
        return [
            'EXT:' . Extension::KEY . '/Resources/Public/JavaScript/CreateAnnotation.js',
        ];
    }
}
