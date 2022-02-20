<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Configuration;

use Brotkrueml\MatomoWidgets\Configuration\Configuration;
use Brotkrueml\MatomoWidgets\Configuration\Configurations;
use PHPUnit\Framework\TestCase;

final class ConfigurationsTest extends TestCase
{
    /**
     * @test
     */
    public function findConfigurationBySiteIdentifierReturnsConfigurationCorrectly(): void
    {
        $configuration1 = new Configuration(
            'some_configuration',
            '',
            '',
            0,
            '',
            [],
            [],
            ''
        );
        $configuration2 = new Configuration(
            'another_configuration',
            '',
            '',
            0,
            '',
            [],
            [],
            ''
        );
        $subject = new Configurations([$configuration1, $configuration2]);

        self::assertSame($configuration1, $subject->findConfigurationBySiteIdentifier('some_configuration'));
        self::assertSame($configuration2, $subject->findConfigurationBySiteIdentifier('another_configuration'));
    }

    /**
     * @test
     */
    public function findConfigurationBySiteIdentifierReturnsNullWhenConfigurationNotAvailable(): void
    {
        $configuration1 = new Configuration(
            'some_configuration',
            '',
            '',
            0,
            '',
            [],
            [],
            ''
        );
        $subject = new Configurations([$configuration1]);

        self::assertNull($subject->findConfigurationBySiteIdentifier('not_existing'));
    }

    /**
     * @test
     */
    public function iteratingOverAvailableConfigurationsIsPossible(): void
    {
        $configuration1 = new Configuration(
            'some_configuration',
            '',
            '',
            0,
            '',
            [],
            [],
            ''
        );
        $configuration2 = new Configuration(
            'another_configuration',
            '',
            '',
            0,
            '',
            [],
            [],
            ''
        );
        $subject = new Configurations([$configuration1, $configuration2]);

        self::assertInstanceOf(\IteratorAggregate::class, $subject);
        $iterator = $subject->getIterator();
        self::assertSame($configuration1, $iterator->current());
        $iterator->next();
        self::assertSame($configuration2, $iterator->current());
    }

    /**
     * @test
     */
    public function countingTheAvailableConfigurationsIsPossible(): void
    {
        $configuration1 = new Configuration(
            'some_configuration',
            '',
            '',
            0,
            '',
            [],
            [],
            ''
        );
        $configuration2 = new Configuration(
            'another_configuration',
            '',
            '',
            0,
            '',
            [],
            [],
            ''
        );
        $subject = new Configurations([$configuration1, $configuration2]);

        self::assertInstanceOf(\Countable::class, $subject);
        self::assertCount(2, $subject);
    }
}
