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
use Brotkrueml\MatomoWidgets\Widgets\BarChartWidget;
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericBarChartDataProvider;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
final class VisitsPerMonthRegistration extends AbstractRegistration
{
    private const METHOD = 'VisitsSummary.getVisits';
    private const PARAMETERS_PARAMETERS = 'matomo_widgets.visitsPerMonth.parameters';

    protected $serviceIdSuffix = 'visitsSummary.visitsPerMonth';

    public function register(): void
    {
        if (!$this->matomoConfiguration->isWidgetActive('visitsPerMonth')) {
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
                'date' => 'last12'
            ]
        );
    }

    private function registerDataProvider(): void
    {
        $this->services
            ->set($this->buildServiceDataProviderId(), GenericBarChartDataProvider::class)
            ->arg('$connectionConfiguration', new Reference($this->connectionConfigurationId))
            ->arg('$method', self::METHOD)
            ->arg(
                '$barLabel',
                Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.visits.dataset.label'
            )
            ->arg('$backgroundColour', '#1a568f')
            ->arg('$parameters', '%' . self::PARAMETERS_PARAMETERS . '%');
    }

    private function registerWidget(): void
    {
        $localisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.visitsPerMonth.title';
        $title = $this->matomoConfiguration->getSiteTitle()
            ? \sprintf('%s: %s', $this->matomoConfiguration->getSiteTitle(), 'Visits per month')
            : $localisedTitle;

        $this->services
            ->set($this->buildServiceWidgetId(), BarChartWidget::class)
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
                    'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.visitsPerMonth.description',
                    'iconIdentifier' => self::ICON_IDENTIFIER,
                    'height' => 'medium',
                    'width' => 'small',
                ]
            );
    }

    private function buildReportLink(): string
    {
        return \sprintf(
            '%s?module=CoreHome&action=index&idSite=%d&period=month&date=today#?segment=&category=General_Visitors&subcategory=General_Overview',
            $this->matomoConfiguration->getUrl(),
            $this->matomoConfiguration->getIdSite()
        );
    }
}
