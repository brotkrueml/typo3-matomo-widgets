<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Cache;

use Brotkrueml\MatomoWidgets\Connection\ConnectionConfiguration;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;

/**
 * @internal
 */
final class CacheIdentifierCreator
{
    public function createEntryIdentifier(ConnectionConfiguration $configuration, string $method, ParameterBag $parameterBag): string
    {
        return \sprintf(
            '%s_%s',
            \str_replace('.', '_', $method),
            \md5(\serialize($configuration) . \serialize($parameterBag)),
        );
    }

    public function createTag(ConnectionConfiguration $configuration, string $method): string
    {
        return \sprintf(
            '%s_%s',
            \str_replace('.', '_', $method),
            \md5(\serialize($configuration)),
        );
    }
}
