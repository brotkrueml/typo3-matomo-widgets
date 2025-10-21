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
use Brotkrueml\MatomoWidgets\Widgets\Provider\AnnotationsTableDataProvider;
use Brotkrueml\MatomoWidgets\Widgets\TableWidget;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
final class AnnotationsRegistration extends AbstractRegistration
{
    private const METHOD = 'Annotations.getAll';
    private const PARAMETERS_PARAMETERS = 'matomo_widgets.annotations.parameters';

    protected $serviceIdSuffix = 'annotations.getAll';

    public function register(): void
    {
        if (! $this->matomoConfiguration->isWidgetActive('annotations')) {
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
                'date' => 'today',
                'lastN' => 365,
            ],
        );
    }

    private function registerDataProvider(): void
    {
        $this->services
            ->set($this->buildServiceDataProviderId(), AnnotationsTableDataProvider::class)
            ->arg('$connectionConfiguration', new Reference($this->connectionConfigurationId))
            ->arg('$method', self::METHOD)
            ->arg(
                '$columns',
                [
                    [
                        'column' => 'date',
                        'header' => Extension::LANGUAGE_PATH_DASHBOARD . ':date',
                    ],
                    [
                        'column' => 'note',
                        'header' => Extension::LANGUAGE_PATH_DASHBOARD . ':note',
                    ],
                ],
            )
            ->arg('$parameters', '%' . self::PARAMETERS_PARAMETERS . '%');
    }

    private function registerWidget(): void
    {
        $localisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.annotations.title';
        $title = $this->matomoConfiguration->siteTitle !== ''
            ? \sprintf('%s: %s', $this->matomoConfiguration->siteTitle, 'Annotations')
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
                    'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.annotations.description',
                    'iconIdentifier' => self::ICON_IDENTIFIER,
                    'height' => 'medium',
                    'width' => 'small',
                ],
            );
    }
}
