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
use Brotkrueml\MatomoWidgets\Widgets\Decorator\CountryFlagDecorator;
use Brotkrueml\MatomoWidgets\Widgets\Decorator\DecoratorInterface;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class CountryFlagDecoratorTest extends TestCase
{
    /**
     * @var Stub|ExtensionConfiguration
     */
    private $extensionConfigurationStub;

    protected function setUp(): void
    {
        $this->extensionConfigurationStub = $this->createStub(ExtensionConfiguration::class);
    }

    private function setUrlInExtensionConfigurationStub(string $url): void
    {
        $this->extensionConfigurationStub
            ->method('get')
            ->with(Extension::KEY)
            ->willReturn(['url' => $url]);
    }

    /**
     * @test
     */
    public function classImplementsDecoratorInterface(): void
    {
        $this->setUrlInExtensionConfigurationStub('https://example.org/');

        self::assertInstanceOf(DecoratorInterface::class, new CountryFlagDecorator($this->extensionConfigurationStub));
    }

    /**
     * @test
     * @dataProvider dataProviderForDecorate
     */
    public function decorate(string $url, string $value, string $expected): void
    {
        $this->setUrlInExtensionConfigurationStub($url);
        $subject = new CountryFlagDecorator($this->extensionConfigurationStub);

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
        $this->setUrlInExtensionConfigurationStub('https://example.org/');
        $subject = new CountryFlagDecorator($this->extensionConfigurationStub);

        self::assertTrue($subject->isHtmlOutput());
    }
}
