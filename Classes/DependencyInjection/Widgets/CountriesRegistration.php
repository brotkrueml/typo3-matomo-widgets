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
use Brotkrueml\MatomoWidgets\Parameter\LanguageParameterResolver;
use Brotkrueml\MatomoWidgets\Widgets\Decorator\CountryFlagDecorator;
use Brotkrueml\MatomoWidgets\Widgets\Decorator\NumberDecorator;
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericTableDataProvider;
use Brotkrueml\MatomoWidgets\Widgets\TableWidget;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
final class CountriesRegistration extends AbstractRegistration
{
    private const METHOD = 'UserCountry.getCountry';
    private const PARAMETERS_PARAMETERS = 'matomo_widgets.country.parameters';

    protected $serviceIdSuffix = 'userCountry.country';

    public function register(): void
    {
        if (!$this->matomoConfiguration->isWidgetActive('countries')) {
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
                'filter_limit' => '50',
                'filter_sort_column' => 'nb_visits',
                'filter_sort_order' => 'desc',
            ]
        );
    }

    private function registerDataProvider(): void
    {
        $countryFlagDecoratorId = 'matomo_widgets.countryFlagDecorator.' . $this->matomoConfiguration->getSiteIdentifier();
        $this->services->set($countryFlagDecoratorId, CountryFlagDecorator::class)
            ->arg('$url', $this->matomoConfiguration->getUrl());

        $this->services
            ->set($this->buildServiceDataProviderId(), GenericTableDataProvider::class)
            ->arg('$connectionConfiguration', new Reference($this->connectionConfigurationId))
            ->arg('$method', self::METHOD)
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
            ->arg('$parameters', '%' . self::PARAMETERS_PARAMETERS . '%')
        ->call('addParameter', ['language', new Reference(LanguageParameterResolver::class)]);
    }

    private function registerWidget(): void
    {
        $localisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.userCountry.country.title';
        $title = $this->matomoConfiguration->getSiteTitle()
            ? \sprintf('%s: %s', $this->matomoConfiguration->getSiteTitle(), 'Campaigns')
            : $localisedTitle;

        $this->services
            ->set($this->buildServiceWidgetId(), TableWidget::class)
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
                    'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.userCountry.country.description',
                    'iconIdentifier' => self::ICON_IDENTIFIER,
                    'height' => 'medium',
                    'width' => 'small',
                ]
            );
    }

    private function buildReportLink(): string
    {
        return \sprintf(
            '%s?module=CoreHome&action=index&idSite=%d&period=month&date=today#?segment=&category=General_Visitors&subcategory=UserCountry_SubmenuLocations',
            $this->matomoConfiguration->getUrl(),
            $this->matomoConfiguration->getIdSite()
        );
    }
}
