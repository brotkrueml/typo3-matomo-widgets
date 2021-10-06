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
    /**
     * @var MatomoRepositoryInterface
     */
    private $repository;
    /**
     * @var FrontendInterface
     */
    private $cache;
    /**
     * @var CacheIdentifierCreator
     */
    private $cacheIdentifierCreator;

    public function __construct(
        MatomoRepositoryInterface $repository,
        FrontendInterface $cache,
        CacheIdentifierCreator $cacheIdentifierCreator
    ) {
        $this->repository = $repository;
        $this->cache = $cache;
        $this->cacheIdentifierCreator = $cacheIdentifierCreator;
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
