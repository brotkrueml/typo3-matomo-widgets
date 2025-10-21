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
use Brotkrueml\MatomoWidgets\Widgets\Decorator\BrowserPluginIconDecorator;
use Brotkrueml\MatomoWidgets\Widgets\Decorator\NumberDecorator;
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericTableDataProvider;
use Brotkrueml\MatomoWidgets\Widgets\TableWidget;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
final class BrowserPluginsRegistration extends AbstractRegistration
{
    private const METHOD = 'DevicePlugins.getPlugin';
    private const PARAMETERS_PARAMETERS = 'matomo_widgets.browserPlugins.parameters';

    protected $serviceIdSuffix = 'devicePlugins.plugin';

    public function register(): void
    {
        if (! $this->matomoConfiguration->isWidgetActive('browserPlugins')) {
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
                'filter_sort_column' => 'nb_visits',
                'filter_sort_order' => 'desc',
            ],
        );
    }

    private function registerDataProvider(): void
    {
        $browserPluginIconDecoratorId = 'matomo_widgets.browserPluginDecorator.' . $this->matomoConfiguration->siteIdentifier;
        $this->services->set($browserPluginIconDecoratorId, BrowserPluginIconDecorator::class)
            ->arg('$url', $this->matomoConfiguration->url);

        $this->services
            ->set($this->buildServiceDataProviderId(), GenericTableDataProvider::class)
            ->arg('$connectionConfiguration', new Reference($this->connectionConfigurationId))
            ->arg('$method', self::METHOD)
            ->arg(
                '$columns',
                [
                    [
                        'column' => 'logo',
                        'decorator' => new Reference($browserPluginIconDecoratorId),
                        'classes' => 'matomo-widgets__browser-plugin__column',
                    ],
                    [
                        'column' => 'label',
                        'header' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.devicePlugins.plugin.column.name',
                    ],
                    [
                        'column' => 'nb_visits_percentage',
                        'header' => Extension::LANGUAGE_PATH_DASHBOARD . ':visitsPercentage',
                        'classes' => 'matomo-widgets__text-end',
                    ],
                    [
                        'column' => 'nb_visits',
                        'header' => Extension::LANGUAGE_PATH_DASHBOARD . ':visits',
                        'decorator' => new Reference(NumberDecorator::class),
                        'classes' => 'matomo-widgets__text-end',
                    ],
                ],
            )
            ->arg('$parameters', '%' . self::PARAMETERS_PARAMETERS . '%');
    }

    private function registerWidget(): void
    {
        $localisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.devicePlugins.plugin.title';
        $title = $this->matomoConfiguration->siteTitle !== ''
            ? \sprintf('%s: %s', $this->matomoConfiguration->siteTitle, 'Browser plugins')
            : $localisedTitle;

        $this->services
            ->set($this->buildServiceWidgetId(), TableWidget::class)
            ->arg('$dataProvider', new Reference($this->buildServiceDataProviderId()))
            ->arg(
                '$options',
                [
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
                    'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.devicePlugins.plugin.description',
                    'iconIdentifier' => self::ICON_IDENTIFIER,
                    'height' => 'medium',
                    'width' => 'small',
                ],
            );
    }
}
