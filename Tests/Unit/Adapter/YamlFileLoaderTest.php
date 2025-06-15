<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Adapter;

use Brotkrueml\MatomoWidgets\Adapter\YamlFileLoader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader as CoreYamlFileLoader;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[CoversClass(YamlFileLoader::class)]
final class YamlFileLoaderTest extends TestCase
{
    protected function setUp(): void
    {
        $logManagerDummy = self::createStub(LogManager::class);
        $logManagerDummy
            ->method('getLogger')
            ->willReturn(new NullLogger());

        GeneralUtility::setSingletonInstance(LogManager::class, $logManagerDummy);
    }

    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();
    }

    #[Test]
    public function getReturnsCoreYamlFileLoaderCorrectly(): void
    {
        // If no error occurs, we are done: the class is correctly build for
        // different TYPO3 version when run on CI
        $actual = YamlFileLoader::get();

        // Although the return declaration already provides the correct class,
        // we check here the output to have an assertion.
        self::assertInstanceOf(CoreYamlFileLoader::class, $actual);
    }
}
