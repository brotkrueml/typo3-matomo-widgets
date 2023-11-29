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
use Brotkrueml\MatomoWidgets\Widgets\Decorator\PagesNotFoundPathDecorator;
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericTableDataProvider;
use Brotkrueml\MatomoWidgets\Widgets\TableWidget;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
final class PagesNotFoundRegistration extends AbstractRegistration
{
    private const METHOD = 'Actions.getPageTitles';
    private const PARAMETERS_PARAMETERS = 'matomo_widgets.pagesNotFound.parameters';

    protected $serviceIdSuffix = 'actions.pagesNotFound';

    public function register(): void
    {
        if (! $this->matomoConfiguration->isWidgetActive('pagesNotFound')) {
            return;
        }

        if ($this->matomoConfiguration->pagesNotFoundTemplate === '') {
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
                'date' => 'last28',
                'filter_limit' => '50',
                'filter_sort_column' => 'nb_hits',
                'filter_sort_order' => 'desc',
            ],
        );
    }

    private function registerDataProvider(): void
    {
        $pathDecoratorId = 'matomo_widgets.pagesNotFoundPathDecorator.' . $this->matomoConfiguration->siteIdentifier;
        $this->services->set($pathDecoratorId, PagesNotFoundPathDecorator::class)
            ->arg('$template', $this->matomoConfiguration->pagesNotFoundTemplate);

        $this->services
            ->set($this->buildServiceDataProviderId(), GenericTableDataProvider::class)
            ->arg('$connectionConfiguration', new Reference($this->connectionConfigurationId))
            ->arg('$method', self::METHOD)
            ->arg(
                '$columns',
                [
                    [
                        'column' => 'label',
                        'header' => Extension::LANGUAGE_PATH_DASHBOARD . ':path',
                        'decorator' => new Reference($pathDecoratorId),
                        'classes' => 'matomo-widgets__break-word',
                    ],
                    [
                        'column' => 'nb_hits',
                        'header' => Extension::LANGUAGE_PATH_DASHBOARD . ':hits',
                        'classes' => 'matomo-widgets__text-end',
                    ],
                ],
            )
            ->arg('$parameters', '%' . self::PARAMETERS_PARAMETERS . '%')
            ->call('addParameter', [
                'filter_column', 'label',
            ])
            ->call('addParameter', [
                'filter_pattern', $this->buildFilterPattern($this->matomoConfiguration->pagesNotFoundTemplate),
            ])
            ->call('addParameter', [
                'showColumns', 'nb_hits',
            ]);
    }

    private function buildFilterPattern(string $template): string
    {
        $replacements = [
            '{statusCode}' => '404',
            '{path}' => '.*?',
            '{referrer}' => '.*?',
        ];

        return \str_replace(\array_keys($replacements), \array_values($replacements), $template);
    }

    private function registerWidget(): void
    {
        $localisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.actions.pagesNotFound.title';
        $title = $this->matomoConfiguration->siteTitle !== ''
            ? \sprintf('%s: %s', $this->matomoConfiguration->siteTitle, 'Pages not found')
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
                    'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.actions.pagesNotFound.description',
                    'iconIdentifier' => self::ICON_IDENTIFIER,
                    'height' => 'medium',
                    'width' => 'medium',
                ],
            );
    }

    private function buildReportLink(): string
    {
        return \sprintf(
            '%s?module=CoreHome&action=index&idSite=%d&period=month&date=today#?period=month&date=today&segment=&category=General_Actions&subcategory=Actions_SubmenuPageTitles',
            $this->matomoConfiguration->url,
            $this->matomoConfiguration->idSite,
        );
    }
}
