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
use Symfony\Component\DependencyInjection\Loader\Configurator\ParametersConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;

/**
 * @internal
 */
abstract class AbstractRegistration
{
    protected const PROVIDER_ID_TEMPLATE = 'dashboard.provider.matomo_widgets.%s.%s';
    protected const SERVICE_ID_TEMPLATE = 'dashboard.widget.matomo_widgets.%s.%s';
    protected const ICON_IDENTIFIER = 'content-widget-matomo';

    /**
     * @var ParametersConfigurator
     */
    protected $parameters;

    /**
     * @var ServicesConfigurator
     */
    protected $services;

    /**
     * @var Configuration
     */
    protected $matomoConfiguration;

    /**
     * @var string
     */
    protected $connectionConfigurationId;

    /**
     * @var string
     */
    protected $serviceIdSuffix = '';

    public function __construct(
        ParametersConfigurator $parameters,
        ServicesConfigurator $services,
        Configuration $matomoConfiguration,
        string $connectionConfigurationId
    ) {
        $this->parameters = $parameters;
        $this->services = $services;
        $this->matomoConfiguration = $matomoConfiguration;
        $this->connectionConfigurationId = $connectionConfigurationId;
    }

    abstract public function register(): void;

    protected function buildServiceDataProviderId(): string
    {
        return \sprintf(
            self::PROVIDER_ID_TEMPLATE,
            $this->matomoConfiguration->getSiteIdentifier(),
            $this->serviceIdSuffix
        );
    }

    protected function buildServiceWidgetId(): string
    {
        return \sprintf(
            self::SERVICE_ID_TEMPLATE,
            $this->matomoConfiguration->getSiteIdentifier(),
            $this->serviceIdSuffix
        );
    }

    protected function buildWidgetIdentifier(): string
    {
        return \sprintf(
            'matomo_widgets.%s.%s',
            $this->matomoConfiguration->getSiteIdentifier(),
            $this->serviceIdSuffix
        );
    }
}
