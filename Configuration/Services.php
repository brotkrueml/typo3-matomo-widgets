<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\MatomoIntegration\Extension as MatomoIntegrationExtension;
use Brotkrueml\MatomoWidgets\DependencyInjection\WidgetsRegistration;
use Brotkrueml\MatomoWidgets\Domain\Repository\CachingRepositoryDecorator;
use Brotkrueml\MatomoWidgets\Domain\Repository\MatomoRepository;
use Brotkrueml\MatomoWidgets\Domain\Repository\RepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

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

    $parameters = $containerConfigurator->parameters();
    $isMatomoIntegrationAvailable = $containerBuilder->hasDefinition(MatomoIntegrationExtension::class);

    (new WidgetsRegistration())->register($services, $parameters, $isMatomoIntegrationAvailable);
};
