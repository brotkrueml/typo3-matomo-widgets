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
final class UrlCount
{
    private int $hits = 0;

    public function __construct(
        private readonly string $url
    ) {
    }

    public function incrementHits(): void
    {
        $this->hits++;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getHits(): int
    {
        return $this->hits;
    }
}
