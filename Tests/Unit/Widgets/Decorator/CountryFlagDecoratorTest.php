<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Widgets\Decorator;

use Brotkrueml\MatomoWidgets\Widgets\Decorator\CountryFlagDecorator;
use Brotkrueml\MatomoWidgets\Widgets\Decorator\DecoratorInterface;
use PHPUnit\Framework\TestCase;

class CountryFlagDecoratorTest extends TestCase
{
    /**
     * @test
     */
    public function classImplementsDecoratorInterface(): void
    {
        self::assertInstanceOf(DecoratorInterface::class, new CountryFlagDecorator('https://example.org/'));
    }

    /**
     * @test
     * @dataProvider dataProviderForDecorate
     */
    public function decorate(string $url, string $value, string $expected): void
    {
        $subject = new CountryFlagDecorator($url);

        self::assertSame($expected, $subject->decorate($value));
    }

    public function dataProviderForDecorate(): \Generator
    {
        yield 'Empty value returns empty result' => [
            'url' => 'https://example.org/',
            'value' => '',
            'expected' => '',
        ];

        yield 'Image URL is not valid, empty string is returned' => [
            'url' => 'https://example.org/',
            'value' => 'not "valid"',
            'expected' => '',
        ];

        yield 'Flag path is provided, image tag is returned' => [
            'url' => 'https://example.org/',
            'value' => 'flags/us.png',
            'expected' => '<img src="https://example.org/flags/us.png" width="24" alt="" class="matomo-widgets__country-flag__image">',
        ];

        yield 'index.php is stripped from url' => [
            'url' => 'https://example.org/index.php',
            'value' => 'flags/us.png',
            'expected' => '<img src="https://example.org/flags/us.png" width="24" alt="" class="matomo-widgets__country-flag__image">',
        ];

        yield 'Missing / is appended to base URL' => [
            'url' => 'https://example.org',
            'value' => 'flags/us.png',
            'expected' => '<img src="https://example.org/flags/us.png" width="24" alt="" class="matomo-widgets__country-flag__image">',
        ];
    }

    /**
     * @test
     */
    public function isHtmlOutputReturnsTrue(): void
    {
        $subject = new CountryFlagDecorator('https://example.org/');

        self::assertTrue($subject->isHtmlOutput());
    }
}
