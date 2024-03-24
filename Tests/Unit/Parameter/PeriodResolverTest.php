<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Parameter;

use Brotkrueml\MatomoWidgets\Extension;
use Brotkrueml\MatomoWidgets\Parameter\PeriodResolver;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Localization\LanguageService;

#[CoversClass(PeriodResolver::class)]
final class PeriodResolverTest extends TestCase
{
    private PeriodResolver $subject;
    private LanguageService&Stub $languageServiceStub;

    protected function setUp(): void
    {
        $this->subject = new PeriodResolver();

        $this->languageServiceStub = $this->createStub(LanguageService::class);
        $GLOBALS['LANG'] = $this->languageServiceStub;
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['LANG']);
    }

    /**
     * @param array{key: string, value: string} $translation
     */
    #[Test]
    #[DataProvider('dataProvider')]
    public function resolve(string $period, string $date, ?array $translation, string $expected): void
    {
        if (\is_array($translation)) {
            $this->languageServiceStub
                ->method('sL')
                ->with(Extension::LANGUAGE_PATH_DASHBOARD . ':' . $translation['key'])
                ->willReturn($translation['value']);
        }

        $actual = $this->subject->resolve($period, $date);

        self::assertSame($expected, $actual);
    }

    public static function dataProvider(): iterable
    {
        yield 'range with lastXX' => [
            'period' => 'range',
            'date' => 'last14',
            'translation' => [
                'key' => 'period.range.last',
                'value' => 'period.range.last %d',
            ],
            'expected' => 'period.range.last 14',
        ];

        yield 'range with previousXX' => [
            'period' => 'range',
            'date' => 'previous14',
            'translation' => [
                'key' => 'period.range.previous',
                'value' => 'period.range.previous %d',
            ],
            'expected' => 'period.range.previous 14',
        ];

        yield 'range with date range' => [
            'period' => 'range',
            'date' => '2024-01-01,2024-01-31',
            'translation' => [
                'key' => 'period.range.range',
                'value' => 'period.range.range %s %s',
            ],
            'expected' => 'period.range.range 2024-01-01 2024-01-31',
        ];

        yield 'range with unsupported date' => [
            'period' => 'range',
            'date' => 'date format unsupported',
            'translation' => null,
            'expected' => 'Range: date format unsupported',
        ];

        yield 'unsupported period' => [
            'period' => 'day',
            'date' => 'last10',
            'translation' => null,
            'expected' => 'Day: last10',
        ];
    }
}
