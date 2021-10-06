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

class CreateAnnotationWidget implements WidgetInterface, AdditionalCssInterface, AdditionalJavaScriptInterface
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

    public function __construct(
        WidgetConfigurationInterface $configuration,
        StandaloneView $view,
        array $options = []
    ) {
        $this->configuration = $this->prefixWithSiteTitle($configuration, $options);
        $this->view = $view;
        $this->options = $options;
    }

    public function renderWidgetContent(): string
    {
        /** @psalm-suppress InternalMethod */
        $this->view->setTemplate('Widget/CreateAnnotationWidget.html');
        $this->view->assignMultiple([
            'configuration' => $this->configuration,
            'reportLink' => $this->options['reportLink'] ?? '',
            'siteIdentifier' => $this->options['siteIdentifier'] ?? '',
        ]);

        return $this->view->render();
    }

    public function getCssFiles(): array
    {
        return [
            \sprintf('EXT:%s/Resources/Public/Css/matomo-widgets.css', Extension::KEY),
        ];
    }

    public function getJsFiles(): array
    {
        return [
            \sprintf('EXT:%s/Resources/Public/JavaScript/CreateAnnotation.js', Extension::KEY),
        ];
    }
}
