<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\DependencyInjection\Widgets;

use Brotkrueml\MatomoWidgets\Extension;
use Brotkrueml\MatomoWidgets\Widgets\DoughnutChartWidget;
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericDoughnutChartDataProvider;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
final class BrowsersRegistration extends AbstractRegistration
{
    private const METHOD = 'DevicesDetection.getBrowsers';
    private const PARAMETERS_LIMIT = 'matomo_widgets.browsers.limit';
    private const PARAMETERS_PARAMETERS = 'matomo_widgets.browsers.parameters';

    protected $serviceIdSuffix = 'devicesDetection.browsers';

    public function register(): void
    {
        if (!$this->matomoConfiguration->isWidgetActive('browsers')) {
            return;
        }

        $this->defineParameters();
        $this->registerDataProvider();
        $this->registerWidget();
    }

    private function defineParameters(): void
    {
        $this->parameters->set(
            self::PARAMETERS_PARAMETERS,
            [
                'period' => 'month',
                'date' => 'today',
                'filter_sort_column' => 'nb_visits',
                'filter_sort_order' => 'desc',
            ]
        );
        $this->parameters->set(self::PARAMETERS_LIMIT, '5');
    }

    private function registerDataProvider(): void
    {
        $this->services
            ->set($this->buildServiceDataProviderId(), GenericDoughnutChartDataProvider::class)
            ->arg('$connectionConfiguration', new Reference($this->connectionConfigurationId))
            ->arg('$method', self::METHOD)
            ->arg('$labelColumn', 'label')
            ->arg('$valueColumn', 'nb_visits')
            ->arg('$limit', '%' . self::PARAMETERS_LIMIT . '%')
            ->arg('$backgroundColours', ['#ff8700', '#a4276a', '#1a568f', '#4c7e3a', '#69bbb5', '#76949f'])
            ->arg('$parameters', '%' . self::PARAMETERS_PARAMETERS . '%');
    }

    private function registerWidget(): void
    {
        $localisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.devicesDetection.browsers.title';
        $title = $this->matomoConfiguration->getSiteTitle()
            ? \sprintf('%s: %s', $this->matomoConfiguration->getSiteTitle(), 'Browsers')
            : $localisedTitle;

        $this->services
            ->set($this->buildServiceWidgetId(), DoughnutChartWidget::class)
            ->arg('$dataProvider', new Reference($this->buildServiceDataProviderId()))
            ->arg('$view', new Reference('dashboard.views.widget'))
            ->arg(
                '$options',
                [
                    'reportLink' => $this->buildReportLink(),
                    'siteTitle' => $this->matomoConfiguration->getSiteTitle(),
                    'title' => $localisedTitle,
                ]
            )
            ->tag(
                'dashboard.widget',
                [
                    'identifier' => $this->buildWidgetIdentifier(),
                    'groupNames' => 'matomo',
                    'title' => $title,
                    'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.devicesDetection.browsers.description',
                    'iconIdentifier' => self::ICON_IDENTIFIER,
                    'height' => 'medium',
                    'width' => 'small',
                ]
            );
    }

    private function buildReportLink(): string
    {
        return \sprintf(
            '%s?module=CoreHome&action=index&idSite=%d&period=month&date=today#?segment=&category=General_Visitors&subcategory=DevicesDetection_Software',
            $this->matomoConfiguration->getUrl(),
            $this->matomoConfiguration->getIdSite()
        );
    }
}
