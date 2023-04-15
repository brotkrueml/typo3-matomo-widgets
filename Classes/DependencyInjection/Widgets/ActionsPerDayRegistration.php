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
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericBarChartDataProvider;
use Symfony\Component\DependencyInjection\Reference;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Dashboard\Widgets\BarChartWidget;

/**
 * @internal
 */
final class ActionsPerDayRegistration extends AbstractRegistration
{
    private const METHOD = 'VisitsSummary.getActions';
    private const PARAMETERS_PARAMETERS = 'matomo_widgets.actionsPerDay.parameters';

    protected $serviceIdSuffix = 'visitsSummary.actionsPerDay';

    public function register(): void
    {
        if (! $this->matomoConfiguration->isWidgetActive('actionsPerDay')) {
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
                'period' => 'day',
                'date' => 'last28',
            ],
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
                Extension::LANGUAGE_PATH_DASHBOARD . ':actions',
            )
            ->arg('$backgroundColour', '#4c7e3a')
            ->arg('$parameters', '%' . self::PARAMETERS_PARAMETERS . '%');
    }

    private function registerWidget(): void
    {
        $localisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.actionsPerDay.title';
        $title = $this->matomoConfiguration->siteTitle !== ''
            ? \sprintf('%s: %s', $this->matomoConfiguration->siteTitle, 'Actions per day')
            : $localisedTitle;

        $configuration = $this->services
            ->set($this->buildServiceWidgetId(), BarChartWidget::class)
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
                    'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.actionsPerDay.description',
                    'iconIdentifier' => self::ICON_IDENTIFIER,
                    'height' => 'medium',
                    'width' => 'medium',
                ],
            );

        if ((new Typo3Version())->getMajorVersion() < 12) {
            $configuration->arg('$view', new Reference('dashboard.views.widget'));
        } else {
            $configuration->arg('$backendViewFactory', new Reference(BackendViewFactory::class));
        }
    }
}
