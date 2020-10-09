<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\MatomoWidgets\Domain\Repository\CachingRepositoryDecorator;
use Brotkrueml\MatomoWidgets\Domain\Repository\MatomoRepository;
use Brotkrueml\MatomoWidgets\Domain\Repository\RepositoryInterface;
use Brotkrueml\MatomoWidgets\Extension;
use Brotkrueml\MatomoWidgets\Parameter\LanguageParameterResolver;
use Brotkrueml\MatomoWidgets\Widgets\Decorator\CountryFlagDecorator;
use Brotkrueml\MatomoWidgets\Widgets\Decorator\NumberDecorator;
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericBarChartDataProvider;
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericDoughnutChartDataProvider;
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericTableDataProvider;
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericValueDataProvider;
use Brotkrueml\MatomoWidgets\Widgets\Provider\LinkMatomoButtonProvider;
use Brotkrueml\MatomoWidgets\Widgets\TableWidget;
use Brotkrueml\MatomoWidgets\Widgets\ValueWithIconWidget;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Dashboard\Widgets\BarChartWidget;
use TYPO3\CMS\Dashboard\Widgets\CtaWidget;
use TYPO3\CMS\Dashboard\Widgets\DoughnutChartWidget;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('matomo_widgets.actionsPerDay.parameters', ['period' => 'day', 'date' => 'last28']);

    $parameters->set('matomo_widgets.actionsPerMonth.parameters', ['period' => 'month', 'date' => 'last12']);

    $parameters->set('matomo_widgets.bounceRate.parameters', ['period' => 'month', 'date' => 'today']);

    $parameters->set(
        'matomo_widgets.bounceRate.subtitle',
        Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.bounceRate.subtitle'
    );

    $parameters->set('matomo_widgets.browsers.limit', '5');

    $parameters->set(
        'matomo_widgets.browsers.parameters',
        [
            'period' => 'month',
            'date' => 'today',
            'filter_sort_column' => 'nb_visits',
            'filter_sort_order' => 'desc',
        ]
    );

    $parameters->set(
        'matomo_widgets.campaigns.parameters',
        [
            'period' => 'month',
            'date' => 'today',
            'filter_limit' => '30',
            'filter_sort_column' => 'nb_visits',
            'filter_sort_order' => 'desc',
        ]
    );

    $parameters->set(
        'matomo_widgets.country.parameters',
        [
            'period' => 'month',
            'date' => 'today',
            'filter_limit' => '50',
            'filter_sort_column' => 'nb_visits',
            'filter_sort_order' => 'desc',
        ]
    );

    $parameters->set('matomo_widgets.osFamilies.limit', '5');

    $parameters->set(
        'matomo_widgets.osFamilies.parameters',
        [
            'period' => 'month',
            'date' => 'today',
            'filter_sort_column' => 'nb_visits',
            'filter_sort_order' => 'desc',
        ]
    );

    $parameters->set(
        'matomo_widgets.visitsPerDay.parameters',
        [
            'period' => 'day',
            'date' => 'last28',
        ]
    );

    $parameters->set(
        'matomo_widgets.visitsPerMonth.parameters',
        [
            'period' => 'month',
            'date' => 'last12',
        ]
    );

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();

    $services->load('Brotkrueml\MatomoWidgets\\', __DIR__ . '/../Classes/*');

    $services->set('cache.matomo_widgets', FrontendInterface::class)
        ->factory([ref(CacheManager::class), 'getCache'])
        ->arg('$identifier', 'matomo_widgets');

    $services->set(CachingRepositoryDecorator::class)
        ->arg('$repository', ref(MatomoRepository::class))
        ->arg('$cache', ref('cache.matomo_widgets'));

    $services->alias(RepositoryInterface::class, CachingRepositoryDecorator::class);

    $services->set('dashboard.provider.brotkrueml.matomo_widgets.visitsSummary.actionsPerDay', GenericBarChartDataProvider::class)
        ->arg('$method', 'VisitsSummary.getActions')
        ->arg(
            '$barLabel',
            Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.actions.dataset.label'
        )
        ->arg('$backgroundColour', '#4c7e3a')
        ->arg('$parameters', '%matomo_widgets.actionsPerDay.parameters%');

    $services->set('dashboard.widget.brotkrueml.matomo_widgets.visitsSummary.actionsPerDay', BarChartWidget::class)
        ->arg('$dataProvider', ref('dashboard.provider.brotkrueml.matomo_widgets.visitsSummary.actionsPerDay'))
        ->arg('$view', ref('dashboard.views.widget'))
        ->tag(
            'dashboard.widget',
            [
                'identifier' => 'matomo_widgets.visitsSummary.actionsPerDay',
                'groupNames' => 'matomo',
                'title' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.actionsPerDay.title',
                'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.actionsPerDay.description',
                'iconIdentifier' => 'content-widget-chart-bar',
                'height' => 'medium',
                'width' => 'medium',
            ]
        );

    $services->set('dashboard.provider.brotkrueml.matomo_widgets.visitsSummary.actionsPerMonth', GenericBarChartDataProvider::class)
        ->arg('$method', 'VisitsSummary.getActions')
        ->arg(
            '$barLabel',
            Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.actions.dataset.label'
        )
        ->arg('$backgroundColour', '#4c7e3a')
        ->arg('$parameters', '%matomo_widgets.actionsPerMonth.parameters%');

    $services->set('dashboard.widget.brotkrueml.matomo_widgets.visitsSummary.actionsPerMonth', BarChartWidget::class)
        ->arg('$dataProvider', ref('dashboard.provider.brotkrueml.matomo_widgets.visitsSummary.actionsPerMonth'))
        ->arg('$view', ref('dashboard.views.widget'))
        ->tag(
            'dashboard.widget',
            [
                'identifier' => 'matomo_widgets.visitsSummary.actionsPerMonth',
                'groupNames' => 'matomo',
                'title' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.actionsPerMonth.title',
                'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.actionsPerMonth.description',
                'iconIdentifier' => 'content-widget-chart-bar',
                'height' => 'medium',
                'width' => 'small',
            ]
        );

    $services->set('dashboard.provider.brotkrueml.matomo_widgets.visitsSummary.visitsPerDay', GenericBarChartDataProvider::class)
        ->arg('$method', 'VisitsSummary.getVisits')
        ->arg(
            '$barLabel',
            Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.visits.dataset.label'
        )
        ->arg('$backgroundColour', '#1a568f')
        ->arg('$parameters', '%matomo_widgets.visitsPerDay.parameters%');

    $services->set('dashboard.widget.brotkrueml.matomo_widgets.visitsSummary.visitsPerDay', BarChartWidget::class)
        ->arg('$dataProvider', ref('dashboard.provider.brotkrueml.matomo_widgets.visitsSummary.visitsPerDay'))
        ->arg('$view', ref('dashboard.views.widget'))
        ->tag(
            'dashboard.widget',
            [
                'identifier' => 'matomo_widgets.visitsSummary.visitsPerDay',
                'groupNames' => 'matomo',
                'title' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.visitsPerDay.title',
                'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.visitsPerDay.description',
                'iconIdentifier' => 'content-widget-chart-bar',
                'height' => 'medium',
                'width' => 'medium',
            ]
        );

    $services->set('dashboard.provider.brotkrueml.matomo_widgets.visitsSummary.visitsPerMonth', GenericBarChartDataProvider::class)
        ->arg('$method', 'VisitsSummary.getVisits')
        ->arg(
            '$barLabel',
            Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.visits.dataset.label'
        )
        ->arg('$backgroundColour', '#1a568f')
        ->arg('$parameters', '%matomo_widgets.visitsPerMonth.parameters%');

    $services->set('dashboard.widget.brotkrueml.matomo_widgets.visitsSummary.visitsPerMonth', BarChartWidget::class)
        ->arg('$dataProvider', ref('dashboard.provider.brotkrueml.matomo_widgets.visitsSummary.visitsPerMonth'))
        ->arg('$view', ref('dashboard.views.widget'))
        ->tag(
            'dashboard.widget',
            [
                'identifier' => 'matomo_widgets.visitsSummary.visitsPerMonth',
                'groupNames' => 'matomo',
                'title' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.visitsPerMonth.title',
                'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.visitsPerMonth.description',
                'iconIdentifier' => 'content-widget-chart-bar',
                'height' => 'medium',
                'width' => 'small',
            ]
        );

    $services->set('dashboard.provider.brotkrueml.matomo_widgets.visitsSummary.bounceRate', GenericValueDataProvider::class)
        ->arg('$method', 'VisitsSummary.get')
        ->arg('$columnName', 'bounce_rate')
        ->arg('$parameters', '%matomo_widgets.bounceRate.parameters%');

    $services->set('dashboard.widget.brotkrueml.matomo_widgets.visitsSummary.bounceRate', ValueWithIconWidget::class)
        ->arg('$dataProvider', ref('dashboard.provider.brotkrueml.matomo_widgets.visitsSummary.bounceRate'))
        ->arg('$view', ref('dashboard.views.widget'))
        ->arg(
            '$options',
            [
                'title' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.bounceRate.title',
                'subtitle' => '%matomo_widgets.bounceRate.subtitle%',
                'icon' => 'content-target',
            ]
        )
        ->tag(
            'dashboard.widget',
            [
                'identifier' => 'matomo_widgets.visitsSummary.bounceRate',
                'groupNames' => 'matomo',
                'title' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.bounceRate.title',
                'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.bounceRate.description',
                'iconIdentifier' => 'content-widget-number',
            ]
        );

    $services->set('dashboard.provider.brotkrueml.matomo_widgets.devicesDetection.browsers', GenericDoughnutChartDataProvider::class)
        ->arg('$method', 'DevicesDetection.getBrowsers')
        ->arg('$labelColumn', 'label')
        ->arg('$valueColumn', 'nb_visits')
        ->arg('$limit', '%matomo_widgets.browsers.limit%')
        ->arg('$backgroundColours', ['#ff8700', '#a4276a', '#1a568f', '#4c7e3a', '#69bbb5', '#76949f'])
        ->arg('$parameters', '%matomo_widgets.browsers.parameters%');

    $services->set('dashboard.widget.brotkrueml.matomo_widgets.devicesDetection.browsers', DoughnutChartWidget::class)
        ->arg('$view', ref('dashboard.views.widget'))
        ->arg('$dataProvider', ref('dashboard.provider.brotkrueml.matomo_widgets.devicesDetection.browsers'))
        ->tag(
            'dashboard.widget',
            [
                'identifier' => 'matomo_widgets.devicesDetection.browsers',
                'groupNames' => 'matomo',
                'title' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.devicesDetection.browsers.title',
                'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.devicesDetection.browsers.description',
                'iconIdentifier' => 'content-widget-chart-pie',
                'height' => 'medium',
                'width' => 'small',
            ]
        );

    $services->set('dashboard.provider.brotkrueml.matomo_widgets.devicesDetection.osFamilies', GenericDoughnutChartDataProvider::class)
        ->arg('$method', 'DevicesDetection.getOsFamilies')
        ->arg('$labelColumn', 'label')
        ->arg('$valueColumn', 'nb_visits')
        ->arg('$limit', '%matomo_widgets.osFamilies.limit%')
        ->arg('$backgroundColours', ['#ff8700', '#a4276a', '#1a568f', '#4c7e3a', '#69bbb5', '#76949f'])
        ->arg('$parameters', '%matomo_widgets.osFamilies.parameters%');

    $services->set('dashboard.widget.brotkrueml.matomo_widgets.devicesDetection.osFamilies', DoughnutChartWidget::class)
        ->arg('$view', ref('dashboard.views.widget'))
        ->arg('$dataProvider', ref('dashboard.provider.brotkrueml.matomo_widgets.devicesDetection.osFamilies'))
        ->tag(
            'dashboard.widget',
            [
                'identifier' => 'matomo_widgets.devicesDetection.osFamilies',
                'groupNames' => 'matomo',
                'title' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.devicesDetection.osFamilies.title',
                'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.devicesDetection.osFamilies.description',
                'iconIdentifier' => 'content-widget-chart-pie',
                'height' => 'medium',
                'width' => 'small',
            ]
        );

    $services->set('dashboard.provider.brotkrueml.matomo_widgets.referrers.campaigns', GenericTableDataProvider::class)
        ->arg('$method', 'Referrers.getCampaigns')
        ->arg(
            '$columns',
            [
                [
                    'column' => 'label',
                    'header' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.referrers.campaigns.column.name',
                ],
                [
                    'column' => 'nb_visits',
                    'header' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.referrers.campaigns.column.visits',
                    'decorator' => ref(NumberDecorator::class),
                    'classes' => 'text-right',
                ],
            ]
        )
        ->arg('$parameters', '%matomo_widgets.campaigns.parameters%');

    $services->set('dashboard.widget.brotkrueml.matomo_widgets.referrers.campaigns', TableWidget::class)
        ->arg('$view', ref('dashboard.views.widget'))
        ->arg('$dataProvider', ref('dashboard.provider.brotkrueml.matomo_widgets.referrers.campaigns'))
        ->tag(
            'dashboard.widget',
            [
                'identifier' => 'matomo_widgets.referrers.campaigns',
                'groupNames' => 'matomo',
                'title' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.referrers.campaigns.title',
                'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.referrers.campaigns.description',
                'iconIdentifier' => 'content-widget-list',
                'height' => 'medium',
                'width' => 'small',
            ]
        );

    $services->set('dashboard.provider.brotkrueml.matomo_widgets.userCountry.country', GenericTableDataProvider::class)
        ->arg('$method', 'UserCountry.getCountry')
        ->arg(
            '$columns',
            [
                ['column' =>
                    'logo',
                    'decorator' => ref(CountryFlagDecorator::class),
                    'classes' => 'matomo-widgets__country-flag__column',
                ],
                [
                    'column' => 'label',
                    'header' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.userCountry.country.column.name',
                    ],
                [
                    'column' => 'nb_visits',
                    'header' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.userCountry.country.column.visits',
                    'decorator' => ref(NumberDecorator::class),
                    'classes' => 'text-right',
                ],
            ]
        )
        ->arg('$parameters', '%matomo_widgets.country.parameters%')
        ->call('addParameter', ['language', ref(LanguageParameterResolver::class)]);

    $services->set('dashboard.widget.brotkrueml.matomo_widgets.userCountry.country', TableWidget::class)
        ->arg('$view', ref('dashboard.views.widget'))
        ->arg(
            '$dataProvider',
            ref('dashboard.provider.brotkrueml.matomo_widgets.userCountry.country')
        )
        ->tag(
            'dashboard.widget',
            ['identifier' => 'matomo_widgets.userCountry.country',
                'groupNames' => 'matomo',
                'title' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.userCountry.country.title',
                'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.userCountry.country.description',
                'iconIdentifier' => 'content-widget-list',
                'height' => 'medium',
                'width' => 'small',
            ]
        );

    $services->set('dashboard.buttons.brotkrueml.matomo_widgets.linkMatomo', LinkMatomoButtonProvider::class);

    $services->set('dashboard.widget.brotkrueml.matomo_widgets.linkMatomo', CtaWidget::class)
        ->arg('$view', ref('dashboard.views.widget'))
        ->arg('$buttonProvider', ref('dashboard.buttons.brotkrueml.matomo_widgets.linkMatomo'))
        ->arg(
            '$options',
            ['text' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.linkMatomo.text']
        )
        ->tag(
            'dashboard.widget',
            [
                'identifier' => 'matomo_widgets.linkMatomo',
                'groupNames' => 'matomo',
                'title' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.linkMatomo.title',
                'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.linkMatomo.description',
                'iconIdentifier' => 'content-widget-text',
                'height' => 'small',
                'width' => 'small',
            ]
        );
};
