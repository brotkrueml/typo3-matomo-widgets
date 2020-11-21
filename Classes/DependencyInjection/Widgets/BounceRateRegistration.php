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
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericValueDataProvider;
use Brotkrueml\MatomoWidgets\Widgets\ValueWithIconWidget;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
final class BounceRateRegistration extends AbstractRegistration
{
    private const METHOD = 'VisitsSummary.get';
    private const PARAMETERS_PARAMETERS = 'matomo_widgets.bounceRate.parameters';
    private const PARAMETERS_SUBTITLE = 'matomo_widgets.bounceRate.subtitle';

    protected $serviceIdSuffix = 'visitsSummary.bounceRate';

    public function register(): void
    {
        if (!$this->matomoConfiguration->isWidgetEnabled('matomoWidgetsEnableBounceRate')) {
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
                'date' => 'today'
            ]
        );
        $this->parameters->set(
            self::PARAMETERS_SUBTITLE,
            Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.bounceRate.subtitle'
        );
    }

    private function registerDataProvider(): void
    {
        $this->services
            ->set($this->buildServiceDataProviderId(), GenericValueDataProvider::class)
            ->arg('$connectionConfiguration', new Reference($this->connectionConfigurationId))
            ->arg('$method', self::METHOD)
            ->arg('$columnName', 'bounce_rate')
            ->arg('$parameters', '%' . self::PARAMETERS_PARAMETERS . '%');
    }

    private function registerWidget(): void
    {
        $localisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.bounceRate.title';
        $title = $this->matomoConfiguration->getSiteTitle()
            ? \sprintf('%s: %s', $this->matomoConfiguration->getSiteTitle(), 'Bounce rate')
            : $localisedTitle;

        $this->services
            ->set($this->buildServiceWidgetId(), ValueWithIconWidget::class)
            ->arg('$dataProvider', new Reference($this->buildServiceDataProviderId()))
            ->arg('$view', new Reference('dashboard.views.widget'))
            ->arg(
                '$options',
                [
                    'siteTitle' => $this->matomoConfiguration->getSiteTitle(),
                    'title' => $localisedTitle,
                    'subtitle' => '%' . self::PARAMETERS_SUBTITLE . '%',
                    'icon' => 'content-target',
                ]
            )
            ->tag(
                'dashboard.widget',
                [
                    'identifier' => $this->buildWidgetIdentifier(),
                    'groupNames' => 'matomo',
                    'title' => $title,
                    'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.bounceRate.description',
                    'iconIdentifier' => self::ICON_IDENTIFIER,
                ]
            );
    }
}
