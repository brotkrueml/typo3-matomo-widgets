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
use Brotkrueml\MatomoWidgets\Widgets\MoversAndShakersWidget;
use Brotkrueml\MatomoWidgets\Widgets\Provider\MoversAndShakersDataProvider;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
final class MoversAndShakersRegistration extends AbstractRegistration
{
    private const METHOD = 'Insights.getMoversAndShakersOverview';
    private const PARAMETERS_PARAMETERS = 'matomo_widgets.moversAndShakers.parameters';

    protected $serviceIdSuffix = 'insights.moversAndShakers';

    public function register(): void
    {
        if (! $this->matomoConfiguration->isWidgetActive('moversAndShakers')) {
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
            ],
        );
    }

    private function registerDataProvider(): void
    {
        $this->services
            ->set($this->buildServiceDataProviderId(), MoversAndShakersDataProvider::class)
            ->arg('$connectionConfiguration', new Reference($this->connectionConfigurationId))
            ->arg('$method', self::METHOD)
            ->arg('$parameters', '%' . self::PARAMETERS_PARAMETERS . '%');
    }

    private function registerWidget(): void
    {
        $localisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.insights.moversAndShakers.title';
        $title = $this->matomoConfiguration->siteTitle !== ''
            ? \sprintf('%s: %s', $this->matomoConfiguration->siteTitle, 'Movers and shakers')
            : $localisedTitle;

        $this->services
            ->set($this->buildServiceWidgetId(), MoversAndShakersWidget::class)
            ->arg('$dataProvider', new Reference($this->buildServiceDataProviderId()))
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
                    'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.insights.moversAndShakers.description',
                    'iconIdentifier' => self::ICON_IDENTIFIER,
                    'height' => 'medium',
                    'width' => 'medium',
                ],
            );
    }

    private function buildReportLink(): string
    {
        return \sprintf(
            '%s?module=CoreHome&action=index&idSite=%d&period=week&date=today#?category=General_Actions&subcategory=General_Pages',
            $this->matomoConfiguration->url,
            $this->matomoConfiguration->idSite,
        );
    }
}
