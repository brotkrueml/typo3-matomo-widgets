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
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericDoughnutChartDataProvider;
use Symfony\Component\DependencyInjection\Reference;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Dashboard\Widgets\DoughnutChartWidget;

/**
 * @internal
 */
final class OsFamiliesRegistration extends AbstractRegistration
{
    private const METHOD = 'DevicesDetection.getOsFamilies';
    private const PARAMETERS_LIMIT = 'matomo_widgets.osFamilies.limit';
    private const PARAMETERS_PARAMETERS = 'matomo_widgets.osFamilies.parameters';

    protected $serviceIdSuffix = 'devicesDetection.osFamilies';

    public function register(): void
    {
        if (! $this->matomoConfiguration->isWidgetActive('osFamilies')) {
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
            ],
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
        $localisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.devicesDetection.osFamilies.title';
        $title = $this->matomoConfiguration->siteTitle !== ''
            ? \sprintf('%s: %s', $this->matomoConfiguration->siteTitle, 'Operating system families')
            : $localisedTitle;

        $configuration = $this->services
            ->set($this->buildServiceWidgetId(), DoughnutChartWidget::class)
            ->arg('$dataProvider', new Reference($this->buildServiceDataProviderId()))
            ->arg('$view', new Reference('dashboard.views.widget'))
            ->arg(
                '$options',
                [
                    'reportLink' => $this->buildReportLink(),
                    'siteTitle' => $this->matomoConfiguration->siteTitle,
                    'title' => $localisedTitle,
                ],
            )
            ->tag(
                'dashboard.widget',
                [
                    'identifier' => $this->buildWidgetIdentifier(),
                    'groupNames' => 'matomo',
                    'title' => $title,
                    'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.devicesDetection.osFamilies.description',
                    'iconIdentifier' => self::ICON_IDENTIFIER,
                    'height' => 'medium',
                    'width' => 'small',
                ],
            );

        if ((new Typo3Version())->getMajorVersion() < 12) {
            $configuration->arg('$view', new Reference('dashboard.views.widget'));
        } else {
            $configuration->arg('$backendViewFactory', new Reference(BackendViewFactory::class));
        }
    }

    private function buildReportLink(): string
    {
        return \sprintf(
            '%s?module=CoreHome&action=index&idSite=%d&period=month&date=today#?segment=&category=General_Visitors&subcategory=DevicesDetection_Software',
            $this->matomoConfiguration->url,
            $this->matomoConfiguration->idSite,
        );
    }
}
