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
use Brotkrueml\MatomoWidgets\Widgets\Decorator\NumberDecorator;
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericTableDataProvider;
use Brotkrueml\MatomoWidgets\Widgets\TableWidget;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
final class CampaignsRegistration extends AbstractRegistration
{
    private const METHOD = 'Referrers.getCampaigns';
    private const PARAMETERS_PARAMETERS = 'matomo_widgets.campaigns.parameters';

    protected $serviceIdSuffix = 'referrers.campaigns';

    public function register(): void
    {
        if (! $this->matomoConfiguration->isWidgetActive('campaigns')) {
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
                'filter_limit' => '30',
                'filter_sort_column' => 'nb_visits',
                'filter_sort_order' => 'desc',
            ]
        );
    }

    private function registerDataProvider(): void
    {
        $this->services
            ->set($this->buildServiceDataProviderId(), GenericTableDataProvider::class)
            ->arg('$connectionConfiguration', new Reference($this->connectionConfigurationId))
            ->arg('$method', self::METHOD)
            ->arg(
                '$columns',
                [
                    [
                        'column' => 'label',
                        'header' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.referrers.campaigns.column.name',
                    ],
                    [
                        'column' => 'nb_visits',
                        'header' => Extension::LANGUAGE_PATH_DASHBOARD . ':visits',
                        'decorator' => new Reference(NumberDecorator::class),
                        'classes' => 'text-right',
                    ],
                ]
            )
            ->arg('$parameters', '%' . self::PARAMETERS_PARAMETERS . '%')
            ->call('addParameter', [
                'showColumns', 'nb_visits',
            ]);
    }

    private function registerWidget(): void
    {
        $localisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.referrers.campaigns.title';
        $title = $this->matomoConfiguration->siteTitle !== ''
            ? \sprintf('%s: %s', $this->matomoConfiguration->siteTitle, 'Campaigns')
            : $localisedTitle;

        $this->services
            ->set($this->buildServiceWidgetId(), TableWidget::class)
            ->arg('$dataProvider', new Reference($this->buildServiceDataProviderId()))
            ->arg('$view', new Reference('dashboard.views.widget'))
            ->arg(
                '$options',
                [
                    'reportLink' => $this->buildReportLink(),
                    'siteTitle' => $this->matomoConfiguration->siteTitle,
                    'title' => $localisedTitle,
                ]
            )
            ->tag(
                'dashboard.widget',
                [
                    'identifier' => $this->buildWidgetIdentifier(),
                    'groupNames' => 'matomo',
                    'title' => $title,
                    'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.referrers.campaigns.description',
                    'iconIdentifier' => self::ICON_IDENTIFIER,
                    'height' => 'medium',
                    'width' => 'small',
                ]
            );
    }

    private function buildReportLink(): string
    {
        return \sprintf(
            '%s?module=CoreHome&action=index&idSite=%d&period=month&date=today#?segment=&category=Referrers_Referrers&subcategory=Referrers_Campaigns',
            $this->matomoConfiguration->url,
            $this->matomoConfiguration->idSite
        );
    }
}
