<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\ViewHelpers;

use Brotkrueml\MatomoWidgets\ViewHelpers\DecorateViewHelper;
use Brotkrueml\MatomoWidgets\Widgets\Decorator\DecoratorInterface;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

class DecorateViewHelperTest extends TestCase
{
    private Stub|RenderingContextInterface $renderingContextStub;

    protected function setUp(): void
    {
        $this->renderingContextStub = $this->createStub(RenderingContextInterface::class);
    }

    /**
     * @test
     * @dataProvider dataProviderForRenderStatic
     */
    public function renderStatic(array $arguments, string $expected): void
    {
        $actual = DecorateViewHelper::renderStatic(
            $arguments,
            static function (): void {
            },
            $this->renderingContextStub
        );

        self::assertSame($expected, $actual);
    }

    public function dataProviderForRenderStatic(): \Generator
    {
        yield 'Given string value is decorated' => [
            'arguments' => [
                'decorator' => $this->getDecoratorStub(),
                'value' => 'bar',
            ],
            'expected' => '---bar---',
        ];

        yield 'Given int value is decorated' => [
            'arguments' => [
                'decorator' => $this->getDecoratorStub(),
                'value' => 42,
            ],
            'expected' => '---42---',
        ];
    }

    protected function getDecoratorStub(): DecoratorInterface
    {
        return new class() implements DecoratorInterface {
            public function decorate(string $value): string
            {
                return \sprintf('---%s---', $value);
            }

            public function isHtmlOutput(): bool
            {
                return false;
            }
        };
    }

    /**
     * @test
     */
    public function renderStaticThrowsExceptionWhenDecoratorImplementsNotDecoratorInterface(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionCode(1594828163);
        $this->expectExceptionMessage('The decorator "stdClass" is not an instance of "Brotkrueml\\MatomoWidgets\\Widgets\\Decorator\\DecoratorInterface"');

        $arguments = [
            'decorator' => new \stdClass(),
            'value' => 'foo',
        ];

        DecorateViewHelper::renderStatic(
            $arguments,
            static function (): void {
            },
            $this->renderingContextStub
        );
    }
}
