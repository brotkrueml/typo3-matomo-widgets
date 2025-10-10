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
readonly class ConnectionConfiguration
{
    public string $tokenAuth;

    public function __construct(
        public string $url,
        public int $idSite,
        #[\SensitiveParameter]
        string $tokenAuth,
    ) {
        $this->tokenAuth = $tokenAuth ?: 'anonymous';
    }
}
