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
    public readonly string $tokenAuth;

    public function __construct(
        public readonly string $url,
        public readonly int $idSite,
        string $tokenAuth
    ) {
        $this->tokenAuth = $tokenAuth ?: 'anonymous';
    }
}
