<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Widgets\Decorator;

use Brotkrueml\MatomoWidgets\Widgets\Decorator\BrowserPluginIconDecorator;
use Brotkrueml\MatomoWidgets\Widgets\Decorator\DecoratorInterface;
use PHPUnit\Framework\TestCase;

class BrowserPluginIconDecoratorTest extends TestCase
{
    /**
     * @test
     */
    public function classImplementsDecoratorInterface(): void
    {
        self::assertInstanceOf(DecoratorInterface::class, new BrowserPluginIconDecorator('https://example.org/'));
    }

    /**
     * @test
     * @dataProvider dataProviderForDecorate
     */
    public function decorate(string $url, string $value, string $expected): void
    {
        $subject = new BrowserPluginIconDecorator($url);

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

        yield 'Plugin icon path is provided, image tag is returned' => [
            'url' => 'https://example.org/',
            'value' => 'plugins/Morpheus/icons/dist/plugins/cookie.png',
            'expected' => '<img src="https://example.org/plugins/Morpheus/icons/dist/plugins/cookie.png" width="16" alt="">',
        ];

        yield 'index.php is stripped from url' => [
            'url' => 'https://example.org/index.php',
            'value' => 'plugins/Morpheus/icons/dist/plugins/cookie.png',
            'expected' => '<img src="https://example.org/plugins/Morpheus/icons/dist/plugins/cookie.png" width="16" alt="">',
        ];

        yield 'Missing / is appended to base URL' => [
            'url' => 'https://example.org',
            'value' => 'plugins/Morpheus/icons/dist/plugins/cookie.png',
            'expected' => '<img src="https://example.org/plugins/Morpheus/icons/dist/plugins/cookie.png" width="16" alt="">',
        ];
    }

    /**
     * @test
     */
    public function isHtmlOutputReturnsTrue(): void
    {
        $subject = new BrowserPluginIconDecorator('https://example.org/');

        self::assertTrue($subject->isHtmlOutput());
    }
}
