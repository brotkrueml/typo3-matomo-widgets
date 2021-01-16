<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Connection;

/**
 * @internal
 */
class ConnectionConfiguration
{
    /**
     * @var int
     */
    private $idSite;

    /**
     * @var string
     */
    private $tokenAuth;

    /**
     * @var string
     */
    private $url;

    public function __construct(string $url, int $idSite, string $tokenAuth)
    {
        $this->url = $url;
        $this->idSite = $idSite;
        $this->tokenAuth = $tokenAuth ?: 'anonymous';
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getIdSite(): int
    {
        return $this->idSite;
    }

    public function getTokenAuth(): string
    {
        return $this->tokenAuth;
    }
}
