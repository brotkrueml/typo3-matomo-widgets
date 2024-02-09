<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Widgets;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Dashboard\Widgets\ButtonProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\RequestAwareWidgetInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;

/**
 * @internal
 */
final class CtaWidget implements WidgetInterface, RequestAwareWidgetInterface
{
    use WidgetTitleAdaptionTrait;

    private readonly WidgetConfigurationInterface $configuration;
    /**
     * @var array<string, string>
     */
    private readonly array $options;
    private ServerRequestInterface $request;

    /**
     * @param array<string, string> $options
     */
    public function __construct(
        WidgetConfigurationInterface $configuration,
        private readonly BackendViewFactory $backendViewFactory,
        private readonly ?ButtonProviderInterface $buttonProvider = null,
        array $options = [],
    ) {
        $this->configuration = $this->prefixWithSiteTitle($configuration, $options);
        $this->options = [...[
            'text' => '',
        ], ...$options];
    }

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    public function renderWidgetContent(): string
    {
        $view = $this->backendViewFactory->create($this->request, ['typo3/cms-dashboard', 'brotkrueml/typo3-matomo-widgets']);
        $view->assignMultiple([
            'text' => $this->options['text'],
            'options' => $this->options,
            'button' => $this->buttonProvider,
            'configuration' => $this->configuration,
        ]);
        return $view->render('Widget/CtaWidget');
    }

    /**
     * @return array<string, string>
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
