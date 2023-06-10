<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Adapter;

use GuzzleHttp\ClientInterface;
use TYPO3\CMS\Core\Information\Typo3Version;

/**
 * @internal
 */
class GuzzleClientFactory
{
    public function getClient(): ClientInterface
    {
        if ((new Typo3Version())->getMajorVersion() >= 12) {
            return (new \TYPO3\CMS\Core\Http\Client\GuzzleClientFactory())->getClient();
        }

        return \TYPO3\CMS\Core\Http\Client\GuzzleClientFactory::getClient();
    }
}
