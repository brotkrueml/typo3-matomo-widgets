<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Widgets\Decorator;

use Brotkrueml\MatomoWidgets\Extension;
use Brotkrueml\MatomoWidgets\Widgets\Decorator\DecoratorInterface;
use Brotkrueml\MatomoWidgets\Widgets\Decorator\NumberDecorator;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Localization\LanguageService;

class NumberDecoratorTest extends TestCase
{
    private NumberDecorator $subject;

    protected function setUp(): void
    {
        $languageServiceStub = $this->createStub(LanguageService::class);
        $languageServiceStub
            ->method('sL')
            ->with(Extension::LANGUAGE_PATH_DASHBOARD . ':thousandsSeparator')
            ->willReturn('.');
        $GLOBALS['LANG'] = $languageServiceStub;

        $this->subject = new NumberDecorator();
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['LANG']);
    }

    /**
     * @test
     */
    public function classImplementsDecoratorInterface(): void
    {
        self::assertInstanceOf(DecoratorInterface::class, $this->subject);
    }

    /**
     * @test
     * @dataProvider dataProviderForDecorate
     */
    public function decorate(string $value, string $expected): void
    {
        self::assertSame($expected, $this->subject->decorate($value));
    }

    public function dataProviderForDecorate(): \Generator
    {
        yield 'Value smaller than 1000 has no separator' => [
            'value' => '123',
            'expected' => '123',
        ];

        yield 'Value 1234 has separator' => [
            'value' => '1234',
            'expected' => '1.234',
        ];

        yield 'Value 1234.567 has no decimals' => [
            'value' => '1234.567',
            'expected' => '1.234',
        ];
    }

    /**
     * @test
     */
    public function isHtmlOutputReturnsTrue(): void
    {
        self::assertFalse($this->subject->isHtmlOutput());
    }
}
