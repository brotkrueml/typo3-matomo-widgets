<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\MatomoIntegration\Extension;
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
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\CustomDimensionsRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\LinkMatomoRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\OsFamiliesRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\SiteSearchKeywordsRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\SiteSearchNoResultKeywordsRegstration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\VisitsPerDayRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\VisitsPerMonthRegistration;
use Brotkrueml\MatomoWidgets\Domain\Repository\CachingRepositoryDecorator;
use Brotkrueml\MatomoWidgets\Domain\Repository\MatomoRepository;
use Brotkrueml\MatomoWidgets\Domain\Repository\RepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

return static function (ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void {
    $services = $containerConfigurator->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();
    $services->load('Brotkrueml\\MatomoWidgets\\', dirname(__DIR__) . '/Classes/*');

    $services->set('cache.matomo_widgets', FrontendInterface::class)
        ->factory([new Reference(CacheManager::class), 'getCache'])
        ->arg('$identifier', 'matomo_widgets');

    $services->set(CachingRepositoryDecorator::class)
        ->arg('$repository', new Reference(MatomoRepository::class))
        ->arg('$cache', new Reference('cache.matomo_widgets'));

    $services->alias(RepositoryInterface::class, CachingRepositoryDecorator::class);

    /** @var YamlFileLoader $yamlFileLoader */
    $yamlFileLoader = GeneralUtility::makeInstance(YamlFileLoader::class);
    $configurationFinder = new ConfigurationFinder(
        Environment::getConfigPath(),
        $containerBuilder->hasDefinition(Extension::class),
        $yamlFileLoader
    );
    foreach ($configurationFinder as $matomoConfiguration) {
        /** @var Configuration $matomoConfiguration */
        $connectionConfigurationId = 'matomo_widgets.connectionConfiguration.' . $matomoConfiguration->getSiteIdentifier();
        $services->set($connectionConfigurationId, ConnectionConfiguration::class)
            ->arg('$url', $matomoConfiguration->getUrl())
            ->arg('$idSite', $matomoConfiguration->getIdSite())
            ->arg('$tokenAuth', $matomoConfiguration->getTokenAuth());

        // Register the standard dashboard widgets
        $parameters = $containerConfigurator->parameters();
        foreach (
            [
                ActionsPerDayRegistration::class,
                ActionsPerMonthRegistration::class,
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
            ] as $registrationClass
        ) {
            (new $registrationClass($parameters, $services, $matomoConfiguration, $connectionConfigurationId))->register();
        }

        // Register the custom dimensions dashboard widgets
        foreach ($matomoConfiguration->getCustomDimensions() as $customDimension) {
            (new CustomDimensionsRegistration($parameters, $services, $matomoConfiguration, $connectionConfigurationId, $customDimension))->register();
        }
    }
};
