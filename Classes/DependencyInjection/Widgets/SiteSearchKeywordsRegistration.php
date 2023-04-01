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
final class SiteSearchKeywordsRegistration extends AbstractRegistration
{
    private const METHOD = 'Actions.getSiteSearchKeywords';
    private const PARAMETERS_PARAMETERS = 'matomo_widgets.siteSearchKeywords.parameters';

    protected $serviceIdSuffix = 'actions.siteSearchKeywords';

    public function register(): void
    {
        if (! $this->matomoConfiguration->isWidgetActive('siteSearchKeywords')) {
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
            ],
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
                        'header' => Extension::LANGUAGE_PATH_DASHBOARD . ':keyword',
                    ],
                    [
                        'column' => 'nb_visits',
                        'header' => Extension::LANGUAGE_PATH_DASHBOARD . ':searches',
                        'decorator' => new Reference(NumberDecorator::class),
                        'classes' => 'text-right',
                    ],
                ],
            )
            ->arg('$parameters', '%' . self::PARAMETERS_PARAMETERS . '%')
            ->call('addParameter', [
                'showColumns', 'nb_visits',
            ]);
    }

    private function registerWidget(): void
    {
        $localisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.actions.siteSearchKeywords.title';
        $title = $this->matomoConfiguration->siteTitle !== ''
            ? \sprintf('%s: %s', $this->matomoConfiguration->siteTitle, 'Site search keywords')
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
                ],
            )
            ->tag(
                'dashboard.widget',
                [
                    'identifier' => $this->buildWidgetIdentifier(),
                    'groupNames' => 'matomo',
                    'title' => $title,
                    'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.actions.siteSearchKeywords.description',
                    'iconIdentifier' => self::ICON_IDENTIFIER,
                    'height' => 'medium',
                    'width' => 'small',
                ],
            );
    }

    private function buildReportLink(): string
    {
        return \sprintf(
            '%s?module=CoreHome&action=index&idSite=%d&period=month&date=today#?category=General_Actions&subcategory=Actions_SubmenuSitesearch',
            $this->matomoConfiguration->url,
            $this->matomoConfiguration->idSite,
        );
    }
}
