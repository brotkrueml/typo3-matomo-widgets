<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php81\Rector\Array_\ArrayToFirstClassCallableRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\SafeDeclareStrictTypesRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/Classes',
        __DIR__ . '/Configuration',
        __DIR__ . '/Tests',
    ])
    ->withPhpSets()
    ->withAutoloadPaths([
        __DIR__ . '/.Build/vendor/autoload.php',
    ])
    ->withImportNames(
        importShortClasses: false,
        removeUnusedImports: true,
    )
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        earlyReturn: true,
        phpunitCodeQuality: true,
    )
    ->withAttributesSets(
        phpunit: true,
    )
    ->withRootFiles()
    ->withSkip([
        ArrayToFirstClassCallableRector::class => [
            __DIR__ . '/Configuration/Services.php',
        ],
        PreferPHPUnitThisCallRector::class,
        SafeDeclareStrictTypesRector::class => [
            __DIR__ . '/ext_emconf.php',
        ],
        __DIR__ . '/Tests/Unit/Connection/MatomoConnectorTest.php',
    ]);
