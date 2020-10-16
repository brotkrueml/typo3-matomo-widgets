<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\MatomoWidgets\Configuration\Configuration;
use Brotkrueml\MatomoWidgets\Configuration\ConfigurationFinder;
use Brotkrueml\MatomoWidgets\Connection\ConnectionConfiguration;
use Brotkrueml\MatomoWidgets\Domain\Repository\CachingRepositoryDecorator;
use Brotkrueml\MatomoWidgets\Domain\Repository\MatomoRepository;
use Brotkrueml\MatomoWidgets\Domain\Repository\RepositoryInterface;
use Brotkrueml\MatomoWidgets\Extension;
use Brotkrueml\MatomoWidgets\Parameter\LanguageParameterResolver;
use Brotkrueml\MatomoWidgets\Widgets\BarChartWidget;
use Brotkrueml\MatomoWidgets\Widgets\CtaWidget;
use Brotkrueml\MatomoWidgets\Widgets\Decorator\CountryFlagDecorator;
use Brotkrueml\MatomoWidgets\Widgets\Decorator\NumberDecorator;
use Brotkrueml\MatomoWidgets\Widgets\DoughnutChartWidget;
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericBarChartDataProvider;
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericDoughnutChartDataProvider;
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericTableDataProvider;
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericValueDataProvider;
use Brotkrueml\MatomoWidgets\Widgets\Provider\LinkMatomoButtonProvider;
use Brotkrueml\MatomoWidgets\Widgets\TableWidget;
use Brotkrueml\MatomoWidgets\Widgets\ValueWithIconWidget;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Core\Environment;

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
        ->factory([new Reference(CacheManager::class), 'getCache'])
        ->arg('$identifier', 'matomo_widgets');

    $services->set(CachingRepositoryDecorator::class)
        ->arg('$repository', new Reference(MatomoRepository::class))
        ->arg('$cache', new Reference('cache.matomo_widgets'));

    $services->alias(RepositoryInterface::class, CachingRepositoryDecorator::class);

    $serviceIdPrefix = 'dashboard.widget.matomo_widgets';
    $providerIdPrefix = 'dashboard.provider.matomo_widgets';
    $configurationFinder = new ConfigurationFinder(Environment::getProjectPath());
    foreach ($configurationFinder as $configuration) {
        /** @var Configuration $configuration */
        $connectionConfiguration = 'matomo_widgets.connectionConfiguration.' . $configuration->getSiteIdentifier();
        $services->set($connectionConfiguration, ConnectionConfiguration::class)
            ->arg('$url', $configuration->getUrl())
            ->arg('$idSite', $configuration->getIdSite())
            ->arg('$tokenAuth', $configuration->getTokenAuth());

        /*
         * Dashboard widget: Actions per day
         */
        if ($configuration->isWidgetEnabled('matomoWidgetsEnableActionsPerDay')) {
            $actionsPerDayDataProviderId = sprintf(
                '%s.%s.visitsSummary.actionsPerDay',
                $providerIdPrefix,
                $configuration->getSiteIdentifier()
            );
            $services->set($actionsPerDayDataProviderId, GenericBarChartDataProvider::class)
                ->arg('$connectionConfiguration', new Reference($connectionConfiguration))
                ->arg('$method', 'VisitsSummary.getActions')
                ->arg(
                    '$barLabel',
                    Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.actions.dataset.label'
                )
                ->arg('$backgroundColour', '#4c7e3a')
                ->arg('$parameters', '%matomo_widgets.actionsPerDay.parameters%');

            $actionsPerDayWidgetId = sprintf(
                '%s.%s.visitsSummary.actionsPerDay',
                $serviceIdPrefix,
                $configuration->getSiteIdentifier()
            );
            $actionsPerDayLocalisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.actionsPerDay.title';
            $actionsPerDayTitle = $configuration->getSiteTitle()
                ? sprintf('%s: %s', $configuration->getSiteTitle(), 'Actions per day')
                : $actionsPerDayLocalisedTitle;
            $services->set($actionsPerDayWidgetId, BarChartWidget::class)
                ->arg('$dataProvider', new Reference($actionsPerDayDataProviderId))
                ->arg('$view', new Reference('dashboard.views.widget'))
                ->arg(
                    '$options',
                    [
                        'siteTitle' => $configuration->getSiteTitle(),
                        'title' => $actionsPerDayLocalisedTitle,
                    ]
                )
                ->tag(
                    'dashboard.widget',
                    [
                        'identifier' => 'matomo_widgets.' . $configuration->getSiteIdentifier() . '.visitsSummary.actionsPerDay',
                        'groupNames' => 'matomo',
                        'title' => $actionsPerDayTitle,
                        'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.actionsPerDay.description',
                        'iconIdentifier' => 'content-widget-chart-bar',
                        'height' => 'medium',
                        'width' => 'medium',
                    ]
                );
        }

        /*
         * Dashboard widget: Actions per month
         */
        if ($configuration->isWidgetEnabled('matomoWidgetsEnableActionsPerMonth')) {
            $actionsPerMonthDataProviderId = sprintf(
                '%s.%s.visitsSummary.actionsPerMonth',
                $providerIdPrefix,
                $configuration->getSiteIdentifier()
            );
            $services->set($actionsPerMonthDataProviderId, GenericBarChartDataProvider::class)
                ->arg('$connectionConfiguration', new Reference($connectionConfiguration))
                ->arg('$method', 'VisitsSummary.getActions')
                ->arg(
                    '$barLabel',
                    Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.actions.dataset.label'
                )
                ->arg('$backgroundColour', '#4c7e3a')
                ->arg('$parameters', '%matomo_widgets.actionsPerMonth.parameters%');

            $actionsPerMonthWidgetId = sprintf(
                '%s.%s.visitsSummary.actionsPerMonth',
                $serviceIdPrefix,
                $configuration->getSiteIdentifier()
            );
            $actionsPerMonthLocalisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.actionsPerMonth.title';
            $actionsPerMonthTitle = $configuration->getSiteTitle()
                ? sprintf('%s: %s', $configuration->getSiteTitle(), 'Actions per month')
                : $actionsPerMonthLocalisedTitle;
            $services->set($actionsPerMonthWidgetId, BarChartWidget::class)
                ->arg('$dataProvider', new Reference($actionsPerMonthDataProviderId))
                ->arg('$view', new Reference('dashboard.views.widget'))
                ->arg(
                    '$options',
                    [
                        'siteTitle' => $configuration->getSiteTitle(),
                        'title' => $actionsPerMonthLocalisedTitle,
                    ]
                )
                ->tag(
                    'dashboard.widget',
                    [
                        'identifier' => 'matomo_widgets.' . $configuration->getSiteIdentifier() . '.visitsSummary.actionsPerMonth',
                        'groupNames' => 'matomo',
                        'title' => $actionsPerMonthTitle,
                        'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.actionsPerMonth.description',
                        'iconIdentifier' => 'content-widget-chart-bar',
                        'height' => 'medium',
                        'width' => 'small',
                    ]
                );
        }

        /*
         * Dashboard widget: Actions per month
         */
        if ($configuration->isWidgetEnabled('matomoWidgetsEnableVisitsPerDay')) {
            $visitsPerDayDataProviderId = sprintf(
                '%s.%s.visitsSummary.visitsPerDay',
                $providerIdPrefix,
                $configuration->getSiteIdentifier()
            );
            $services->set($visitsPerDayDataProviderId, GenericBarChartDataProvider::class)
                ->arg('$connectionConfiguration', new Reference($connectionConfiguration))
                ->arg('$method', 'VisitsSummary.getVisits')
                ->arg(
                    '$barLabel',
                    Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.visits.dataset.label'
                )
                ->arg('$backgroundColour', '#1a568f')
                ->arg('$parameters', '%matomo_widgets.visitsPerDay.parameters%');

            $visitsPerDayWidgetId = sprintf(
                '%s.%s.visitsSummary.visitsPerDay',
                $serviceIdPrefix,
                $configuration->getSiteIdentifier()
            );
            $visitsPerDayLocalisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.visitsPerDay.title';
            $visitsPerDayTitle = $configuration->getSiteTitle()
                ? sprintf('%s: %s', $configuration->getSiteTitle(), 'Visits per day')
                : $visitsPerDayLocalisedTitle;
            $services->set($visitsPerDayWidgetId, BarChartWidget::class)
                ->arg('$dataProvider', new Reference($visitsPerDayDataProviderId))
                ->arg('$view', new Reference('dashboard.views.widget'))
                ->arg(
                    '$options',
                    [
                        'siteTitle' => $configuration->getSiteTitle(),
                        'title' => $visitsPerDayLocalisedTitle,
                    ]
                )
                ->tag(
                    'dashboard.widget',
                    [
                        'identifier' => 'matomo_widgets.' . $configuration->getSiteIdentifier() . '.visitsSummary.visitsPerDay',
                        'groupNames' => 'matomo',
                        'title' => $visitsPerDayTitle,
                        'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.visitsPerDay.description',
                        'iconIdentifier' => 'content-widget-chart-bar',
                        'height' => 'medium',
                        'width' => 'medium',
                    ]
                );
        }
        /*
         * Dashboard widget: Visits per month
         */
        if ($configuration->isWidgetEnabled('matomoWidgetsEnableVisitsPerMonth')) {
            $visitsPerMonthDataProviderId = sprintf(
                '%s.%s.visitsSummary.visitsPerMonth',
                $providerIdPrefix,
                $configuration->getSiteIdentifier()
            );
            $services->set($visitsPerMonthDataProviderId, GenericBarChartDataProvider::class)
                ->arg('$connectionConfiguration', new Reference($connectionConfiguration))
                ->arg('$method', 'VisitsSummary.getVisits')
                ->arg(
                    '$barLabel',
                    Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.visits.dataset.label'
                )
                ->arg('$backgroundColour', '#1a568f')
                ->arg('$parameters', '%matomo_widgets.visitsPerMonth.parameters%');

            $visitsPerMonthWidgetId = sprintf(
                '%s.%s.visitsSummary.visitsPerMonth',
                $serviceIdPrefix,
                $configuration->getSiteIdentifier()
            );
            $visitsPerMonthLocalisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.visitsPerMonth.title';
            $visitsPerMonthTitle = $configuration->getSiteTitle()
                ? sprintf('%s: %s', $configuration->getSiteTitle(), 'Visits per month')
                : $visitsPerMonthLocalisedTitle;
            $services->set($visitsPerMonthWidgetId, BarChartWidget::class)
                ->arg('$dataProvider', new Reference($visitsPerMonthDataProviderId))
                ->arg('$view', new Reference('dashboard.views.widget'))
                ->arg(
                    '$options',
                    [
                        'siteTitle' => $configuration->getSiteTitle(),
                        'title' => $visitsPerMonthLocalisedTitle,
                    ]
                )
                ->tag(
                    'dashboard.widget',
                    [
                        'identifier' => 'matomo_widgets.' . $configuration->getSiteIdentifier() . '.visitsSummary.visitsPerMonth',
                        'groupNames' => 'matomo',
                        'title' => $visitsPerMonthTitle,
                        'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.visitsPerMonth.description',
                        'iconIdentifier' => 'content-widget-chart-bar',
                        'height' => 'medium',
                        'width' => 'small',
                    ]
                );
        }

        /*
         * Dashboard widget: Bounce rate
         */
        if ($configuration->isWidgetEnabled('matomoWidgetsEnableBounceRate')) {
            $bounceRateDataProviderId = sprintf(
                '%s.%s.visitsSummary.bounceRate',
                $providerIdPrefix,
                $configuration->getSiteIdentifier()
            );
            $services->set($bounceRateDataProviderId, GenericValueDataProvider::class)
                ->arg('$connectionConfiguration', new Reference($connectionConfiguration))
                ->arg('$method', 'VisitsSummary.get')
                ->arg('$columnName', 'bounce_rate')
                ->arg('$parameters', '%matomo_widgets.bounceRate.parameters%');

            $bounceRateWidgetId = sprintf(
                '%s.%s.visitsSummary.bounceRate',
                $serviceIdPrefix,
                $configuration->getSiteIdentifier()
            );
            $bounceRateLocalisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.bounceRate.title';
            $bounceRateTitle = $configuration->getSiteTitle()
                ? sprintf('%s: %s', $configuration->getSiteTitle(), 'Bounce rate')
                : $bounceRateLocalisedTitle;
            $services->set($bounceRateWidgetId, ValueWithIconWidget::class)
                ->arg('$dataProvider', new Reference($bounceRateDataProviderId))
                ->arg('$view', new Reference('dashboard.views.widget'))
                ->arg(
                    '$options',
                    [
                        'siteTitle' => $configuration->getSiteTitle(),
                        'title' => $bounceRateLocalisedTitle,
                        'subtitle' => '%matomo_widgets.bounceRate.subtitle%',
                        'icon' => 'content-target',
                    ]
                )
                ->tag(
                    'dashboard.widget',
                    [
                        'identifier' => 'matomo_widgets.' . $configuration->getSiteIdentifier() . '.visitsSummary.bounceRate',
                        'groupNames' => 'matomo',
                        'title' => $bounceRateTitle,
                        'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.bounceRate.description',
                        'iconIdentifier' => 'content-widget-number',
                    ]
                );
        }

        /*
         * Dashboard widget: Browsers
         */
        if ($configuration->isWidgetEnabled('matomoWidgetsEnableBrowsers')) {
            $browsersDataProviderId = sprintf(
                '%s.%s.devicesDetection.browsers',
                $providerIdPrefix,
                $configuration->getSiteIdentifier()
            );
            $services->set($browsersDataProviderId, GenericDoughnutChartDataProvider::class)
                ->arg('$connectionConfiguration', new Reference($connectionConfiguration))
                ->arg('$method', 'DevicesDetection.getBrowsers')
                ->arg('$labelColumn', 'label')
                ->arg('$valueColumn', 'nb_visits')
                ->arg('$limit', '%matomo_widgets.browsers.limit%')
                ->arg('$backgroundColours', ['#ff8700', '#a4276a', '#1a568f', '#4c7e3a', '#69bbb5', '#76949f'])
                ->arg('$parameters', '%matomo_widgets.browsers.parameters%');

            $browsersWidgetId = sprintf(
                '%s.%s.devicesDetection.browsers',
                $serviceIdPrefix,
                $configuration->getSiteIdentifier()
            );
            $browsersLocalisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.devicesDetection.browsers.title';
            $browsersTitle = $configuration->getSiteTitle()
                ? sprintf('%s: %s', $configuration->getSiteTitle(), 'Browsers')
                : $browsersLocalisedTitle;
            $services->set($browsersWidgetId, DoughnutChartWidget::class)
                ->arg('$view', new Reference('dashboard.views.widget'))
                ->arg('$dataProvider', new Reference($browsersDataProviderId))
                ->arg(
                    '$options',
                    [
                        'siteTitle' => $configuration->getSiteTitle(),
                        'title' => $browsersLocalisedTitle,
                    ]
                )
                ->tag(
                    'dashboard.widget',
                    [
                        'identifier' => 'matomo_widgets.' . $configuration->getSiteIdentifier() . '.devicesDetection.browsers',
                        'groupNames' => 'matomo',
                        'title' => $browsersTitle,
                        'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.devicesDetection.browsers.description',
                        'iconIdentifier' => 'content-widget-chart-pie',
                        'height' => 'medium',
                        'width' => 'small',
                    ]
                );
        }

        /*
         * Dashboard widget: OS families
         */
        if ($configuration->isWidgetEnabled('matomoWidgetsEnableOsFamilies')) {
            $osFamiliesDataProviderId = sprintf(
                '%s.%s.devicesDetection.osFamilies',
                $providerIdPrefix,
                $configuration->getSiteIdentifier()
            );
            $services->set($osFamiliesDataProviderId, GenericDoughnutChartDataProvider::class)
                ->arg('$connectionConfiguration', new Reference($connectionConfiguration))
                ->arg('$method', 'DevicesDetection.getOsFamilies')
                ->arg('$labelColumn', 'label')
                ->arg('$valueColumn', 'nb_visits')
                ->arg('$limit', '%matomo_widgets.osFamilies.limit%')
                ->arg('$backgroundColours', ['#ff8700', '#a4276a', '#1a568f', '#4c7e3a', '#69bbb5', '#76949f'])
                ->arg('$parameters', '%matomo_widgets.osFamilies.parameters%');

            $osFamiliesWidgetId = sprintf(
                '%s.%s.devicesDetection.osFamilies',
                $serviceIdPrefix,
                $configuration->getSiteIdentifier()
            );
            $osFamiliesLocalisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.devicesDetection.osFamilies.title';
            $osFamiliesTitle = $configuration->getSiteTitle()
                ? sprintf('%s: %s', $configuration->getSiteTitle(), 'Operating system families')
                : $osFamiliesLocalisedTitle;
            $services->set($osFamiliesWidgetId, DoughnutChartWidget::class)
                ->arg('$view', new Reference('dashboard.views.widget'))
                ->arg('$dataProvider', new Reference($osFamiliesDataProviderId))
                ->arg(
                    '$options',
                    [
                        'siteTitle' => $configuration->getSiteTitle(),
                        'title' => $osFamiliesLocalisedTitle,
                    ]
                )
                ->tag(
                    'dashboard.widget',
                    [
                        'identifier' => 'matomo_widgets.' . $configuration->getSiteIdentifier() . '.devicesDetection.osFamilies',
                        'groupNames' => 'matomo',
                        'title' => $osFamiliesTitle,
                        'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.devicesDetection.osFamilies.description',
                        'iconIdentifier' => 'content-widget-chart-pie',
                        'height' => 'medium',
                        'width' => 'small',
                    ]
                );
        }

        /*
         * Dashboard widget: Campaigns
         */
        if ($configuration->isWidgetEnabled('matomoWidgetsEnableCampaigns')) {
            $campaignsDataProviderId = sprintf(
                '%s.%s.referrers.campaigns',
                $providerIdPrefix,
                $configuration->getSiteIdentifier()
            );
            $services->set($campaignsDataProviderId, GenericTableDataProvider::class)
                ->arg('$connectionConfiguration', new Reference($connectionConfiguration))
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
                            'decorator' => new Reference(NumberDecorator::class),
                            'classes' => 'text-right',
                        ],
                    ]
                )
                ->arg('$parameters', '%matomo_widgets.campaigns.parameters%');

            $campaignsWidgetId = sprintf(
                '%s.%s.referrers.campaigns',
                $serviceIdPrefix,
                $configuration->getSiteIdentifier()
            );
            $campaignsLocalisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.referrers.campaigns.title';
            $campaignsTitle = $configuration->getSiteTitle()
                ? sprintf('%s: %s', $configuration->getSiteTitle(), 'Campaigns')
                : $campaignsLocalisedTitle;
            $services->set($campaignsWidgetId, TableWidget::class)
                ->arg('$view', new Reference('dashboard.views.widget'))
                ->arg('$dataProvider', new Reference($campaignsDataProviderId))
                ->arg(
                    '$options',
                    [
                        'siteTitle' => $configuration->getSiteTitle(),
                        'title' => $campaignsLocalisedTitle,
                    ]
                )
                ->tag(
                    'dashboard.widget',
                    [
                        'identifier' => 'matomo_widgets.' . $configuration->getSiteIdentifier() . '.referrers.campaigns',
                        'groupNames' => 'matomo',
                        'title' => $campaignsTitle,
                        'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.referrers.campaigns.description',
                        'iconIdentifier' => 'content-widget-list',
                        'height' => 'medium',
                        'width' => 'small',
                    ]
                );
        }

        /*
         * Dashboard widget: Countries
         */
        if ($configuration->isWidgetEnabled('matomoWidgetsEnableCountries')) {
            $countryFlagDecoratorId = 'matomo_widgets.countryFlagDecorator.' . $configuration->getSiteIdentifier();
            $services->set($countryFlagDecoratorId, CountryFlagDecorator::class)
                ->arg('$url', $configuration->getUrl());

            $countriesDataProviderId = sprintf(
                '%s.%s.userCountry.country',
                $providerIdPrefix,
                $configuration->getSiteIdentifier()
            );
            $services->set($countriesDataProviderId, GenericTableDataProvider::class)
                ->arg('$connectionConfiguration', new Reference($connectionConfiguration))
                ->arg('$method', 'UserCountry.getCountry')
                ->arg(
                    '$columns',
                    [
                        ['column' =>
                            'logo',
                            'decorator' => new Reference($countryFlagDecoratorId),
                            'classes' => 'matomo-widgets__country-flag__column',
                        ],
                        [
                            'column' => 'label',
                            'header' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.userCountry.country.column.name',
                        ],
                        [
                            'column' => 'nb_visits',
                            'header' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.userCountry.country.column.visits',
                            'decorator' => new Reference(NumberDecorator::class),
                            'classes' => 'text-right',
                        ],
                    ]
                )
                ->arg('$parameters', '%matomo_widgets.country.parameters%')
                ->call('addParameter', ['language', new Reference(LanguageParameterResolver::class)]);

            $countriesWidgetId = sprintf(
                '%s.%s.userCountry.country',
                $serviceIdPrefix,
                $configuration->getSiteIdentifier()
            );
            $countriesLocalisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.userCountry.country.title';
            $countriesTitle = $configuration->getSiteTitle()
                ? sprintf('%s: %s', $configuration->getSiteTitle(), 'Countries')
                : $countriesLocalisedTitle;
            $services->set($countriesWidgetId, TableWidget::class)
                ->arg('$view', new Reference('dashboard.views.widget'))
                ->arg('$dataProvider', new Reference($countriesDataProviderId))
                ->arg(
                    '$options',
                    [
                        'siteTitle' => $configuration->getSiteTitle(),
                        'title' => $countriesLocalisedTitle,
                    ]
                )
                ->tag(
                    'dashboard.widget',
                    [
                        'identifier' => 'matomo_widgets.' . $configuration->getSiteIdentifier() . '.userCountry.country',
                        'groupNames' => 'matomo',
                        'title' => $countriesTitle,
                        'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.userCountry.country.description',
                        'iconIdentifier' => 'content-widget-list',
                        'height' => 'medium',
                        'width' => 'small',
                    ]
                );
        }

        /*
         * Dashboard widget: Link to Matomo
         */
        if ($configuration->isWidgetEnabled('matomoWidgetsEnableLinkMatomo')) {
            $linkMatomoDataProviderId = sprintf(
                '%s.%s.linkMatomo',
                $providerIdPrefix,
                $configuration->getSiteIdentifier()
            );

            $services->set($linkMatomoDataProviderId, LinkMatomoButtonProvider::class)
                ->arg('$link', $configuration->getUrl());

            $linkMatomoWidgetId = sprintf(
                '%s.%s.linkMatomo',
                $serviceIdPrefix,
                $configuration->getSiteIdentifier()
            );
            $linkMatomoLocalisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.linkMatomo.title';
            $linkMatomoTitle = $configuration->getSiteTitle()
                ? sprintf('%s: %s', $configuration->getSiteTitle(), 'Link to Matomo')
                : $linkMatomoLocalisedTitle;
            $services->set($linkMatomoWidgetId, CtaWidget::class)
                ->arg('$view', new Reference('dashboard.views.widget'))
                ->arg('$buttonProvider', new Reference($linkMatomoDataProviderId))
                ->arg(
                    '$options',
                    [
                        'siteTitle' => $configuration->getSiteTitle(),
                        'title' => $linkMatomoLocalisedTitle,
                        'text' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.linkMatomo.text',
                    ]
                )
                ->tag(
                    'dashboard.widget',
                    [
                        'identifier' => 'matomo_widgets.' . $configuration->getSiteIdentifier() . '.linkMatomo',
                        'groupNames' => 'matomo',
                        'title' => $linkMatomoTitle,
                        'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.linkMatomo.description',
                        'iconIdentifier' => 'content-widget-text',
                        'height' => 'small',
                        'width' => 'small',
                    ]
                );
        }
    }
};
