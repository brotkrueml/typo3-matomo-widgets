<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Domain\Entity;

/**
 * @internal
 */
final class BrowserCount
{
    private string $name;
    private string $icon;
    /**
     * @var array<string, int>
     */
    private array $versions = [];
    private int $hits = 0;

    public function __construct(string $name, string $icon)
    {
        $this->name = $name;
        $this->icon = $icon;
    }

    public function incrementHit(string $version): void
    {
        $this->hits++;

        if (! ($this->versions[$version] ?? false)) {
            $this->versions[$version] = 0;
        }
        $this->versions[$version]++;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getHits(): int
    {
        return $this->hits;
    }

    public function getVersions(): string
    {
        \arsort($this->versions, \SORT_NUMERIC);

        return \implode(
            ', ',
            \array_map(static fn (string $version, int $count): string => "${version} (${count})", \array_keys($this->versions), \array_values($this->versions))
        );
    }
}
