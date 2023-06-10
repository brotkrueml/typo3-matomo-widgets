<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Array_\CallableThisArrayToAnonymousFunctionRector;
use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector;
use Rector\Php81\Rector\Array_\FirstClassCallableRector;
use Rector\PHPUnit\Set\PHPUnitLevelSetList;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $config): void {
    $config->phpVersion(PhpVersion::PHP_81);

    $config->sets([
        LevelSetList::UP_TO_PHP_81,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        PHPUnitLevelSetList::UP_TO_PHPUNIT_100,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
        PHPUnitSetList::PHPUNIT_EXCEPTION,
        PHPUnitSetList::PHPUNIT_SPECIFIC_METHOD,
        PHPUnitSetList::PHPUNIT_YIELD_DATA_PROVIDER,
    ]);

    $config->autoloadPaths([
        __DIR__ . '/.Build/vendor/autoload.php'
    ]);
    $config->paths([
        __DIR__ . '/Classes',
        __DIR__ . '/Configuration',
        __DIR__ . '/Tests',
    ]);
    $config->skip([
        AddLiteralSeparatorToNumberRector::class,
        CallableThisArrayToAnonymousFunctionRector::class => [
            __DIR__ . '/Configuration/Services.php',
        ],
        FirstClassCallableRector::class => [
            __DIR__ . '/Configuration/Services.php',
        ],
        __DIR__ . '/Classes/Adapter/GuzzleClientFactory.php',
        __DIR__ . '/Tests/Unit/Connection/MatomoConnectorTest.php',
    ]);
};
