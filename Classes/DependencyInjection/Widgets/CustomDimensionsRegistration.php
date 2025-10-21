<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\DependencyInjection\Widgets;

use Brotkrueml\MatomoWidgets\Configuration\Configuration;
use Brotkrueml\MatomoWidgets\Domain\Entity\CustomDimension;
use Brotkrueml\MatomoWidgets\Extension;
use Brotkrueml\MatomoWidgets\Widgets\Decorator\NumberDecorator;
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericTableDataProvider;
use Brotkrueml\MatomoWidgets\Widgets\TableWidget;
use Symfony\Component\DependencyInjection\Loader\Configurator\ParametersConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use TYPO3\CMS\Core\Localization\LanguageService;

/**
 * @internal
 */
final class CustomDimensionsRegistration extends AbstractRegistration
{
    protected const PROVIDER_ID_TEMPLATE = 'dashboard.provider.matomo_widgets.%s.%s.%d';
    protected const SERVICE_ID_TEMPLATE = 'dashboard.widget.matomo_widgets.%s.%s.%d';
    private const METHOD = 'CustomDimensions.getCustomDimension';
    private const PARAMETERS_PARAMETERS_TEMPLATE = 'matomo_widgets.customDimension%d.parameters';

    protected $serviceIdSuffix = 'customDimensions.customDimension';
    private readonly string $parametersName;

    public function __construct(
        ParametersConfigurator $parameters,
        ServicesConfigurator $services,
        Configuration $matomoConfiguration,
        string $connectionConfigurationId,
        private readonly CustomDimension $customDimension,
    ) {
        parent::__construct($parameters, $services, $matomoConfiguration, $connectionConfigurationId);
        $this->parametersName = \sprintf(self::PARAMETERS_PARAMETERS_TEMPLATE, $this->customDimension->idDimension);
    }

    public function register(): void
    {
        $this->defineParameters();
        $this->registerDataProvider();
        $this->registerWidget();
    }

    private function defineParameters(): void
    {
        $this->parameters->set(
            $this->parametersName,
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
        $this->services
            ->set(
                $this->buildServiceDataProviderId(),
                GenericTableDataProvider::class,
            )
            ->arg('$connectionConfiguration', new Reference($this->connectionConfigurationId))
            ->arg('$method', self::METHOD)
            ->arg('$columns', $this->buildColumns())
            ->arg('$parameters', '%' . $this->parametersName . '%')
            ->call('addParameter', ['idDimension', $this->customDimension->idDimension]);
    }

    /**
     * @return list<array{column: string, header: string, decorator?: Reference, classes?: string}>
     */
    private function buildColumns(): array
    {
        $columns = [
            [
                'column' => 'label',
                'header' => $this->customDimension->title,
            ],
        ];

        if ($this->customDimension->scope === 'visit') {
            $columns[] = [
                'column' => 'nb_visits',
                'header' => Extension::LANGUAGE_PATH_DASHBOARD . ':visits',
                'decorator' => new Reference(NumberDecorator::class),
                'classes' => 'matomo-widgets__text-end',
            ];
        }

        $columns[] = [
            'column' => $this->customDimension->scope === 'visit' ? 'nb_actions' : 'nb_hits',
            'header' => Extension::LANGUAGE_PATH_DASHBOARD . ':actions',
            'decorator' => new Reference(NumberDecorator::class),
            'classes' => 'matomo-widgets__text-end',
        ];

        return $columns;
    }

    protected function buildServiceDataProviderId(): string
    {
        return \sprintf(
            self::PROVIDER_ID_TEMPLATE,
            $this->matomoConfiguration->siteIdentifier,
            $this->serviceIdSuffix,
            $this->customDimension->idDimension,
        );
    }

    private function registerWidget(): void
    {
        $title = $localisedTitle = $this->customDimension->title;
        if ($this->matomoConfiguration->siteTitle !== '') {
            $title = \sprintf(
                '%s: %s',
                $this->matomoConfiguration->siteTitle,
                $this->getLanguageService()->sL($title),
            );
        }

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
                    'description' => $this->customDimension->description,
                    'iconIdentifier' => self::ICON_IDENTIFIER,
                    'height' => 'medium',
                    'width' => 'small',
                ],
            );
    }

    protected function buildServiceWidgetId(): string
    {
        return \sprintf(
            self::SERVICE_ID_TEMPLATE,
            $this->matomoConfiguration->siteIdentifier,
            $this->serviceIdSuffix,
            $this->customDimension->idDimension,
        );
    }

    protected function buildWidgetIdentifier(): string
    {
        return \sprintf(
            'matomo_widgets.%s.%s.%d',
            $this->matomoConfiguration->siteIdentifier,
            $this->serviceIdSuffix,
            $this->customDimension->idDimension,
        );
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
