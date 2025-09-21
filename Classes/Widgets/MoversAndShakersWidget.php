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
use Brotkrueml\MatomoWidgets\Widgets\Provider\MoversAndShakersDataProvider;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface;
use TYPO3\CMS\Dashboard\Widgets\RequestAwareWidgetInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;

/**
 * @internal
 */
class MoversAndShakersWidget implements WidgetInterface, AdditionalCssInterface, RequestAwareWidgetInterface
{
    use WidgetTitleAdaptionTrait;

    private readonly WidgetConfigurationInterface $configuration;
    private ServerRequestInterface $request;

    /**
     * @param array<string, string> $options
     */
    public function __construct(
        WidgetConfigurationInterface $configuration,
        private readonly MoversAndShakersDataProvider $dataProvider,
        private readonly BackendViewFactory $backendViewFactory,
        private readonly array $options = [],
    ) {
        $this->configuration = $this->prefixWithSiteTitle($configuration, $options);
    }

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    public function renderWidgetContent(): string
    {
        $rows = $this->dataProvider->getRows();

        $view = $this->backendViewFactory->create($this->request, ['typo3/cms-dashboard', 'brotkrueml/typo3-matomo-widgets']);
        $view->assignMultiple([
            'rows' => $rows,
            'isDataAvailable' => $this->isDataAvailable($rows),
            'configuration' => $this->configuration,
            'reportLink' => $this->options['reportLink'] ?? '',
            'datePeriod' => $this->dataProvider->getDatePeriod(),
        ]);

        return $view->render('Widget/MoversAndShakersWidget.html');
    }

    /**
     * @return array<string, string>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return list<non-empty-string>
     */
    public function getCssFiles(): array
    {
        return [
            'EXT:' . Extension::KEY . '/Resources/Public/Css/matomo-widgets.css',
        ];
    }

    /**
     * @todo Replace with array_any(), once minimum compatibility is set to PHP 8.4 (or symfony/polyfill-php84 is available by default)
     * @param array<string, list<array<string, string|int|bool>>> $rows
     */
    private function isDataAvailable(array $rows): bool
    {
        foreach ($rows as $areaRows) {
            if ($areaRows !== []) {
                return true;
            }
        }

        return false;
    }
}
