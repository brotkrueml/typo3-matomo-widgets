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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

#[CoversClass(DecorateViewHelper::class)]
final class DecorateViewHelperTest extends TestCase
{
    #[Test]
    #[DataProvider('dataProviderForRender')]
    public function render(array $arguments, string $expected): void
    {
        $subject = new DecorateViewHelper();
        $subject->setArguments($arguments);

        $actual = $subject->render();

        self::assertSame($expected, $actual);
    }

    public static function dataProviderForRender(): iterable
    {
        yield 'Given string value is decorated' => [
            'arguments' => [
                'decorator' => self::getDecoratorStub(),
                'value' => 'bar',
            ],
            'expected' => '---bar---',
        ];

        yield 'Given int value is decorated' => [
            'arguments' => [
                'decorator' => self::getDecoratorStub(),
                'value' => 42,
            ],
            'expected' => '---42---',
        ];
    }

    private static function getDecoratorStub(): DecoratorInterface
    {
        return new class implements DecoratorInterface {
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

    #[Test]
    public function renderThrowsExceptionWhenDecoratorImplementsNotDecoratorInterface(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionCode(1594828163);
        $this->expectExceptionMessage('The decorator "stdClass" is not an instance of "Brotkrueml\MatomoWidgets\Widgets\Decorator\DecoratorInterface"');

        $arguments = [
            'decorator' => new \stdClass(),
            'value' => 'foo',
        ];

        $subject = new DecorateViewHelper();
        $subject->setArguments($arguments);

        $subject->render();
    }
}
