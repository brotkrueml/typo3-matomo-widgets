<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Domain\Validation;

use Brotkrueml\MatomoWidgets\Domain\Validation\CustomDimensionConfigurationValidator;
use Brotkrueml\MatomoWidgets\Exception\ValidationException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(CustomDimensionConfigurationValidator::class)]
final class CustomDimensionConfigurationValidatorTest extends TestCase
{
    private CustomDimensionConfigurationValidator $subject;

    protected function setUp(): void
    {
        $this->subject = new CustomDimensionConfigurationValidator();
    }

    #[Test]
    #[DataProvider('dataProviderForProvokingException')]
    public function validateThrowsExceptionOnError(string|array $configuration, int $expectedExceptionCode, string $expectedExceptionMessage): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionCode($expectedExceptionCode);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->subject->validate($configuration);
    }

    public static function dataProviderForProvokingException(): iterable
    {
        yield 'Configuration is not an array' => [
            'configuration' => 'not an array',
            'expectedExceptionCode' => 1618591877,
            'expectedExceptionMessage' => 'A custom dimension configuration is not an array, "string" given',
        ];

        yield 'No scope given' => [
            'configuration' => [],
            'expectedExceptionCode' => 1618591878,
            'expectedExceptionMessage' => 'A custom dimension configuration has no "scope" given',
        ];

        yield 'Invalid scope given' => [
            'configuration' => [
                'scope' => 'invalid',
            ],
            'expectedExceptionCode' => 1618591879,
            'expectedExceptionMessage' => 'A custom dimension configuration has an invalid scope given: "invalid", allowed: action,visit',
        ];

        yield 'no idDimension given' => [
            'configuration' => [
                'scope' => 'action',
            ],
            'expectedExceptionCode' => 1618591880,
            'expectedExceptionMessage' => 'A custom dimension configuration has no "idDimension" given',
        ];

        yield 'Non-numeric idDimension given' => [
            'configuration' => [
                'scope' => 'action',
                'idDimension' => 'foo',
            ],
            'expectedExceptionCode' => 1618591881,
            'expectedExceptionMessage' => 'A custom dimension configuration has a non-numeric "idDimension" parameter "foo"',
        ];

        yield 'Invalid idDimension given' => [
            'configuration' => [
                'scope' => 'action',
                'idDimension' => '0',
            ],
            'expectedExceptionCode' => 1618591882,
            'expectedExceptionMessage' => 'A custom dimension configuration has an invalid "idDimension" parameter "0"',
        ];
    }

    #[Test]
    public function validateThrowsExceptionWithDuplicateIdDimension(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionCode(1618591883);
        $this->expectExceptionMessage('The parameter "idDimension" with the value "42" is already configured');

        $this->subject->validate([
            'scope' => 'action',
            'idDimension' => 42,
        ]);

        $this->subject->validate([
            'scope' => 'action',
            'idDimension' => 42,
        ]);
    }

    #[Test]
    #[DataProvider('dataProviderForCorrectConfiguration')]
    public function validateReturnsTrueIfConfigurationIsCorrect(array $configuration): void
    {
        self::assertTrue($this->subject->validate($configuration));
    }

    public static function dataProviderForCorrectConfiguration(): iterable
    {
        yield 'scope "action" is accepted' => [
            'configuration' => [
                'scope' => 'action',
                'idDimension' => 42,
            ],
        ];

        yield 'scope "visit" is accepted' => [
            'configuration' => [
                'scope' => 'visit',
                'idDimension' => 42,
            ],
        ];

        yield 'idDimension "1" as string is accepted' => [
            'configuration' => [
                'scope' => 'action',
                'idDimension' => '1',
            ],
        ];

        yield 'idDimension 1 as integer is accepted' => [
            'configuration' => [
                'scope' => 'action',
                'idDimension' => 1,
            ],
        ];
    }
}
