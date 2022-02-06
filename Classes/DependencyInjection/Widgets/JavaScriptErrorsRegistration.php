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
use Brotkrueml\MatomoWidgets\Widgets\Decorator\JavaScriptErrorDecorator;
use Brotkrueml\MatomoWidgets\Widgets\JavaScriptErrorsWidget;
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericTableDataProvider;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
final class JavaScriptErrorsRegistration extends AbstractRegistration
{
    private const METHOD = 'Events.getName';
    public const PARAMETERS_PARAMETERS = 'matomo_widgets.javaScriptErrors.parameters';

    protected $serviceIdSuffix = 'events.javaScriptErrors';

    public function register(): void
    {
        if (! $this->matomoConfiguration->isWidgetActive('javaScriptErrors')) {
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
                'period' => 'range',
                'date' => 'last14',
                'filter_limit' => '50',
                'filter_sort_column' => 'nb_events',
                'filter_sort_order' => 'desc',
            ]
        );
    }

    private function registerDataProvider(): void
    {
        $javaScriptErrorDecoratorId = 'matomo_widgets.javaScriptErrorDecorator.' . $this->matomoConfiguration->getSiteIdentifier();
        $this->services->set($javaScriptErrorDecoratorId, JavaScriptErrorDecorator::class)
            ->arg('$siteIdentifier', $this->matomoConfiguration->getSiteIdentifier());

        $this->services
            ->set($this->buildServiceDataProviderId(), GenericTableDataProvider::class)
            ->arg('$connectionConfiguration', new Reference($this->connectionConfigurationId))
            ->arg('$method', self::METHOD)
            ->arg(
                '$columns',
                [
                    [
                        'column' => 'Events_EventName',
                        'header' => Extension::LANGUAGE_PATH_DASHBOARD . ':errorMessage',
                        'decorator' => new Reference($javaScriptErrorDecoratorId),
                        'classes' => 'matomo-widgets__break-word',
                    ],
                    [
                        'column' => 'nb_events',
                        'header' => Extension::LANGUAGE_PATH_DASHBOARD . ':hits',
                        'classes' => 'text-right',
                    ],
                ]
            )
            ->arg('$parameters', '%' . self::PARAMETERS_PARAMETERS . '%')
            ->call('addParameter', [
                'segment', 'eventCategory==JavaScript Errors',
            ])
            ->call('addParameter', [
                'secondaryDimension', 'eventCategory',
            ])
            ->call('addParameter', [
                'flat', '1',
            ])
            ->call('addParameter', [
                'showColumns', 'nb_events',
            ]);
    }

    private function registerWidget(): void
    {
        $localisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.events.javaScriptErrors.title';
        $title = $this->matomoConfiguration->getSiteTitle()
            ? \sprintf('%s: %s', $this->matomoConfiguration->getSiteTitle(), 'JavaScript errors')
            : $localisedTitle;

        $this->services
            ->set($this->buildServiceWidgetId(), JavaScriptErrorsWidget::class)
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
                    'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.events.javaScriptErrors.description',
                    'iconIdentifier' => self::ICON_IDENTIFIER,
                    'height' => 'medium',
                    'width' => 'medium',
                ]
            );
    }

    private function buildReportLink(): string
    {
        return \sprintf(
            '%s?module=CoreHome&action=index&idSite=%d&period=month&date=today#?period=month&date=today&segment=&category=General_Actions&subcategory=Events_Events',
            $this->matomoConfiguration->getUrl(),
            $this->matomoConfiguration->getIdSite()
        );
    }
}
