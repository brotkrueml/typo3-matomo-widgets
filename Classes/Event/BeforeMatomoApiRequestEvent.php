<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Event;

final class BeforeMatomoApiRequestEvent
{
    public function __construct(
        private int $idSite,
        private string $tokenAuth
    ) {
    }

    public function getIdSite(): int
    {
        return $this->idSite;
    }

    public function setIdSite(int $idSite): void
    {
        $this->idSite = $idSite;
    }

    public function getTokenAuth(): string
    {
        return $this->tokenAuth;
    }

    public function setTokenAuth(string $tokenAuth): void
    {
        $this->tokenAuth = $tokenAuth;
    }
}
