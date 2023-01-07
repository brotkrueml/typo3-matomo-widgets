<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Domain\Repository;

use Brotkrueml\MatomoWidgets\Cache\CacheIdentifierCreator;
use Brotkrueml\MatomoWidgets\Connection\ConnectionConfiguration;
use Brotkrueml\MatomoWidgets\Connection\MatomoConnector;
use Brotkrueml\MatomoWidgets\Event\BeforeMatomoApiRequestEvent;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

/**
 * Retrieved data from the Matomo Reporting API dependent on the method
 * @see https://developer.matomo.org/api-reference/reporting-api
 * @internal
 */
class MatomoRepository
{
    private FrontendInterface $cache;
    private CacheIdentifierCreator $cacheIdentifierCreator;
    private MatomoConnector $connector;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        FrontendInterface $cache,
        CacheIdentifierCreator $cacheIdentifierCreator,
        MatomoConnector $connector,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->cache = $cache;
        $this->cacheIdentifierCreator = $cacheIdentifierCreator;
        $this->connector = $connector;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function send(ConnectionConfiguration $configuration, string $method, ParameterBag $parameterBag, bool $useCache = true): array
    {
        $configuration = $this->dispatchEvent($configuration);

        if ($useCache) {
            $cacheIdentifier = $this->cacheIdentifierCreator->createEntryIdentifier($configuration, $method, $parameterBag);
            $tag = $this->cacheIdentifierCreator->createTag($configuration, $method);
            $data = $this->cache->get($cacheIdentifier);
            if ($data === false) {
                $data = $this->connector->callApi($configuration, $method, $parameterBag);
                $this->cache->set($cacheIdentifier, $data, [$tag]);
            }

            return $data;
        }

        return $this->connector->callApi($configuration, $method, $parameterBag);
    }

    private function dispatchEvent(ConnectionConfiguration $configuration): ConnectionConfiguration
    {
        /** @var BeforeMatomoApiRequestEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new BeforeMatomoApiRequestEvent($configuration->getIdSite(), $configuration->getTokenAuth())
        );

        return new ConnectionConfiguration(
            $configuration->getUrl(),
            $event->getIdSite(),
            $event->getTokenAuth()
        );
    }
}
