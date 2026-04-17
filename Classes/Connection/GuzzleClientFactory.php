<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Connection;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;

/**
 * The class applies the $GLOBALS['TYPO3_CONF_VARS']['HTTP'] settings, for example, for using a proxy.
 * @see https://github.com/brotkrueml/typo3-matomo-widgets/issues/46
 *
 * Inspired from \TYPO3\CMS\Core\Http\Client\GuzzleClientFactory
 * @internal
 */
final readonly class GuzzleClientFactory
{
    public function getClient(): ClientInterface
    {
        $httpOptions = $GLOBALS['TYPO3_CONF_VARS']['HTTP'];
        $httpOptions['verify'] = \filter_var($httpOptions['verify'], \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE) ?? $httpOptions['verify'];

        // We remove the TYPO3-special "allowed_hosts" key (currently only necessary for EXT:webhooks)
        unset($httpOptions['allowed_hosts']);

        $stack = ($httpOptions['handler'] ?? null) instanceof HandlerStack ? $httpOptions['handler'] : HandlerStack::create();
        if (\is_array($httpOptions['handler'] ?? null)) {
            foreach ($httpOptions['handler'] as $name => $handler) {
                $stack->push($handler, (string) $name);
            }
        }
        $httpOptions['handler'] = $stack;

        // We have to remove a possibly set auth configuration as it overrides otherwise our authorization header
        // @see https://github.com/brotkrueml/typo3-matomo-widgets/issues/67
        unset($httpOptions['auth']);

        return new Client($httpOptions);
    }
}
