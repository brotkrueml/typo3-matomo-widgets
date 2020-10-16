<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Domain\Repository;

use Brotkrueml\MatomoWidgets\Connection\ConnectionConfiguration;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

class CachingRepositoryDecorator implements RepositoryInterface
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var FrontendInterface
     */
    private $cache;

    public function __construct(RepositoryInterface $repository, FrontendInterface $cache)
    {
        $this->repository = $repository;
        $this->cache = $cache;
    }

    public function find(ConnectionConfiguration $configuration, string $method, ParameterBag $parameterBag): array
    {
        $cacheIdentifier = \sprintf(
            '%s_%s',
            \str_replace('.', '_', $method),
            \md5(\serialize($configuration) . \serialize($parameterBag))
        );
        $data = $this->cache->get($cacheIdentifier);
        if (false === $data) {
            $data = $this->repository->find($configuration, $method, $parameterBag);
            $this->cache->set($cacheIdentifier, $data);
        }

        return $data;
    }
}
