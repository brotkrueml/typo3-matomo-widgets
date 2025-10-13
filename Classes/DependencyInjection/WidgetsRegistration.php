<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\DependencyInjection;

use Brotkrueml\MatomoWidgets\Configuration\Configurations;
use Brotkrueml\MatomoWidgets\Configuration\Widgets;
use Brotkrueml\MatomoWidgets\Connection\ConnectionConfiguration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\CustomDimensionsRegistration;
use Symfony\Component\DependencyInjection\Loader\Configurator\ParametersConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;

/**
 * @internal
 */
final readonly class WidgetsRegistration
{
    public function register(
        ServicesConfigurator $services,
        ParametersConfigurator $parameters,
        Configurations $configurations,
    ): void {
        foreach ($configurations as $configuration) {
            $connectionConfigurationId = 'matomo_widgets.connectionConfiguration.' . $configuration->siteIdentifier;
            $services->set($connectionConfigurationId, ConnectionConfiguration::class)
                ->arg('$url', $configuration->url)
                ->arg('$idSite', $configuration->idSite)
                ->arg('$tokenAuth', $configuration->tokenAuth);

            // Register the dashboard widgets
            foreach (Widgets::cases() as $widget) {
                (new ($widget->getAssociatedClassName())($parameters, $services, $configuration, $connectionConfigurationId))->register();
            }

            // Register the custom dimensions dashboard widgets
            foreach ($configuration->customDimensions as $customDimension) {
                (new CustomDimensionsRegistration($parameters, $services, $configuration, $connectionConfigurationId, $customDimension))->register();
            }
        }
    }
}
