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
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

/**
 * @internal
 */
class CachingMatomoRepositoryDecorator implements MatomoRepositoryInterface
{
    public function __construct(
        private readonly MatomoRepositoryInterface $repository,
        private readonly FrontendInterface $cache,
        private readonly CacheIdentifierCreator $cacheIdentifierCreator
    ) {
    }

    public function send(ConnectionConfiguration $configuration, string $method, ParameterBag $parameterBag): array
    {
        $cacheIdentifier = $this->cacheIdentifierCreator->createEntryIdentifier($configuration, $method, $parameterBag);
        $tag = $this->cacheIdentifierCreator->createTag($configuration, $method);
        $data = $this->cache->get($cacheIdentifier);
        if ($data === false) {
            $data = $this->repository->send($configuration, $method, $parameterBag);
            $this->cache->set($cacheIdentifier, $data, [$tag]);
        }

        return $data;
    }
}
