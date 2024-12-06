<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Adapter;

use Psr\Log\NullLogger;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader as CoreYamlFileLoader;
use TYPO3\CMS\Core\Information\Typo3Version;

/**
 * Adapter around TYPO3 Core's YamlFileLoader:
 * - in TYPO3 v12 the LoggerAwareInterface is implemented
 * - in TYPO3 v13 the logger is injected via constructor
 * @todo Remove this class once compatibility with TYPO3 v12 is dropped.
 *       Also remove this file from parameters.excludePaths in phpstan.neon.
 * @internal
 */
final class YamlFileLoader
{
    public static function get(): CoreYamlFileLoader
    {
        $logger = new NullLogger();
        if ((new Typo3Version())->getMajorVersion() === 12) {
            $yamlFileLoader = new CoreYamlFileLoader();
            $yamlFileLoader->setLogger($logger);
        } else {
            $yamlFileLoader = new CoreYamlFileLoader($logger);
        }

        return $yamlFileLoader;
    }
}
