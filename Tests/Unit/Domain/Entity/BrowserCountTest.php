<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Domain\Entity;

use Brotkrueml\MatomoWidgets\Domain\Entity\BrowserCount;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(BrowserCount::class)]
final class BrowserCountTest extends TestCase
{
    public BrowserCount $subject;

    protected function setUp(): void
    {
        $this->subject = new BrowserCount('some name', 'some icon');
    }

    #[Test]
    public function getNameReturnsNameCorrectly(): void
    {
        self::assertSame('some name', $this->subject->getName());
    }

    #[Test]
    public function getIconReturnsNameCorrectly(): void
    {
        self::assertSame('some icon', $this->subject->getIcon());
    }

    #[Test]
    public function getHitsReturns0AfterInitialisation(): void
    {
        self::assertSame(0, $this->subject->getHits());
    }

    #[Test]
    public function incrementHitIncrementsCorrectly(): void
    {
        $this->subject->incrementHit('42');

        self::assertSame(1, $this->subject->getHits());

        $this->subject->incrementHit('42');

        self::assertSame(2, $this->subject->getHits());
    }

    #[Test]
    #[DataProvider('providerForGetVersions')]
    public function getVersionsReturnsVersionInformationCorrectly(array $versions, string $expected): void
    {
        foreach ($versions as $version) {
            $this->subject->incrementHit($version);
        }

        self::assertSame($expected, $this->subject->getVersions());
    }

    public static function providerForGetVersions(): iterable
    {
        yield 'With one version and one hit' => [
            'versions' => [
                '42',
            ],
            'expected' => '42 (1)',
        ];

        yield 'With one version and two hits' => [
            'versions' => [
                '42',
                '42',
            ],
            'expected' => '42 (2)',
        ];

        yield 'With two version' => [
            'versions' => [
                '42',
                '43',
            ],
            'expected' => '42 (1), 43 (1)',
        ];

        yield 'With three versions and ordered correctly by hits' => [
            'versions' => [
                '42',
                '43',
                '42',
                '44',
                '44',
                '44',
            ],
            'expected' => '44 (3), 42 (2), 43 (1)',
        ];
    }
}
