<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Configuration;

/**
 * @implements \IteratorAggregate<Configuration>
 * @internal
 */
final class Configurations implements \IteratorAggregate, \Countable
{
    /**
     * @var Configuration[]
     */
    private array $configurations = [];

    /**
     * @param Configuration[] $configurations
     */
    public function __construct(array $configurations)
    {
        foreach ($configurations as $configuration) {
            $this->configurations[$configuration->getSiteIdentifier()] = $configuration;
        }
    }

    public function findConfigurationBySiteIdentifier(string $siteIdentifier): ?Configuration
    {
        return $this->configurations[$siteIdentifier] ?? null;
    }

    /**
     * @return \ArrayIterator<int, Configuration>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator(\array_values($this->configurations));
    }

    public function count(): int
    {
        return \count($this->configurations);
    }
}
