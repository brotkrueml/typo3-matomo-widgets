<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Adapter;

use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader as CoreYamlFileLoader;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Adapter around TYPO3 Core's YamlFileLoader:
 * - in TYPO3 v12 the LoggerAwareInterface is implemented
 * - in TYPO3 v13 the logger is injected via constructor
 * @todo This class may be removed once compatibility with TYPO3 v12 is dropped.
 *       Then also remove this file from parameters.excludePaths in phpstan.neon.
 * @internal
 */
final class YamlFileLoader
{
    public static function get(): CoreYamlFileLoader
    {
        if ((new Typo3Version())->getMajorVersion() === 12) {
            $coreYamlFileLoader = new CoreYamlFileLoader();
            $coreYamlFileLoader->setLogger(self::getLogger());
        } else {
            $coreYamlFileLoader = new CoreYamlFileLoader(self::getLogger());
        }

        return $coreYamlFileLoader;
    }

    private static function getLogger(): LoggerInterface
    {
        /** @var LogManager $logManager */
        $logManager = GeneralUtility::makeInstance(LogManager::class);

        return $logManager->getLogger(self::class);
    }
}
