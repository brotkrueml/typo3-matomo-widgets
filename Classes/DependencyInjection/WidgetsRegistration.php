<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\DependencyInjection;

use Brotkrueml\MatomoWidgets\Configuration\Configuration;
use Brotkrueml\MatomoWidgets\Configuration\ConfigurationFinder;
use Brotkrueml\MatomoWidgets\Connection\ConnectionConfiguration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\ActionsPerDayRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\ActionsPerMonthRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\AnnotationsRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\BounceRateRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\BrowserPluginsRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\BrowsersRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\CampaignsRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\ContentNamesRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\ContentPiecesRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\CountriesRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\CreateAnnotationRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\CustomDimensionsRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\LinkMatomoRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\OsFamiliesRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\SiteSearchKeywordsRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\SiteSearchNoResultKeywordsRegstration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\VisitsPerDayRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\VisitsPerMonthRegistration;
use Symfony\Component\DependencyInjection\Loader\Configurator\ParametersConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use TYPO3\CMS\Core\Core\Environment;

/**
 * @internal
 */
final class WidgetsRegistration
{
    private const REGISTRATION_CLASSES = [
        ActionsPerDayRegistration::class,
        ActionsPerMonthRegistration::class,
        //        CreateAnnotationRegistration::class,
        AnnotationsRegistration::class,
        BounceRateRegistration::class,
        BrowserPluginsRegistration::class,
        BrowsersRegistration::class,
        CampaignsRegistration::class,
        ContentNamesRegistration::class,
        ContentPiecesRegistration::class,
        CountriesRegistration::class,
        LinkMatomoRegistration::class,
        OsFamiliesRegistration::class,
        SiteSearchKeywordsRegistration::class,
        SiteSearchNoResultKeywordsRegstration::class,
        VisitsPerDayRegistration::class,
        VisitsPerMonthRegistration::class,
    ];

    public function register(
        ServicesConfigurator $services,
        ParametersConfigurator $parameters,
        bool $isMatomoIntegrationAvailable
    ): void {
        $configurationFinder = new ConfigurationFinder(Environment::getConfigPath(), $isMatomoIntegrationAvailable);
        foreach ($configurationFinder as $matomoConfiguration) {
            /** @var Configuration $matomoConfiguration */
            $connectionConfigurationId = 'matomo_widgets.connectionConfiguration.' . $matomoConfiguration->getSiteIdentifier();
            $services->set($connectionConfigurationId, ConnectionConfiguration::class)
                ->arg('$url', $matomoConfiguration->getUrl())
                ->arg('$idSite', $matomoConfiguration->getIdSite())
                ->arg('$tokenAuth', $matomoConfiguration->getTokenAuth());

            // Register the standard dashboard widgets
            foreach (self::REGISTRATION_CLASSES as $registrationClass) {
                (new $registrationClass($parameters, $services, $matomoConfiguration, $connectionConfigurationId))->register();
            }

            // Register the custom dimensions dashboard widgets
            foreach ($matomoConfiguration->getCustomDimensions() as $customDimension) {
                (new CustomDimensionsRegistration($parameters, $services, $matomoConfiguration, $connectionConfigurationId, $customDimension))->register();
            }
        }
    }
}
