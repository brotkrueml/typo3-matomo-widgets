<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Parameter;

use Brotkrueml\MatomoWidgets\Exception\ParameterNotFoundException;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use Brotkrueml\MatomoWidgets\Parameter\ParameterResolverInterface;
use PHPUnit\Framework\TestCase;

class ParameterBagTest extends TestCase
{
    /**
     * @var ParameterBag
     */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new ParameterBag();
    }

    /**
     * @test
     */
    public function __constructWithParametersAddsThemCorrectly(): void
    {
        $subject = new ParameterBag(['foo' => 'bar', 'qux' => 'quu', 'int' => 42]);

        self::assertSame('bar', $subject->get('foo'));
        self::assertSame('quu', $subject->get('qux'));
        self::assertSame('42', $subject->get('int'));
    }

    /**
     * @test
     */
    public function addOnceAndGetReturnsAddedValues(): void
    {
        $this->subject->add(['foo' => 'bar', 'qux' => 'quu']);

        self::assertSame('bar', $this->subject->get('foo'));
        self::assertSame('quu', $this->subject->get('qux'));
    }

    /**
     * @test
     */
    public function addTwiceAndGetReturnsLastSetValue(): void
    {
        $this->subject->add(['foo' => 'bar']);
        $this->subject->add(['foo' => 'qux']);

        self::assertSame('qux', $this->subject->get('foo'));
    }

    /**
     * @test
     */
    public function addReturnSameInstanceOfParameterBag(): void
    {
        $actual = $this->subject->add(['foo' => 'bar']);

        self::assertSame($this->subject, $actual);
    }

    /**
     * @test
     */
    public function setOnceWithValueStringAndGetReturnsSetValue(): void
    {
        $this->subject->set('foo', 'bar');

        self::assertSame('bar', $this->subject->get('foo'));
    }

    /**
     * @test
     */
    public function setTwiceWithValueStringAndGetReturnsLastSetValue(): void
    {
        $this->subject->set('foo', 'bar');
        $this->subject->set('foo', 'qux');

        self::assertSame('qux', $this->subject->get('foo'));
    }

    /**
     * @test
     */
    public function setWithParameterResolverClassResolvesValue(): void
    {
        $resolver = new class() implements ParameterResolverInterface {
            public function resolve(): string
            {
                return 'resolved value';
            }
        };

        $this->subject->set('foo', $resolver);

        self::assertSame('resolved value', $this->subject->get('foo'));
    }

    /**
     * @test
     */
    public function setReturnSameInstanceOfParameterBag(): void
    {
        $actual = $this->subject->set('foo', 'bar');

        self::assertSame($this->subject, $actual);
    }

    /**
     * @test
     */
    public function setWithClassAsValueThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1594742873);
        $this->expectExceptionMessage('Value must be of type string, int or an instance of "Brotkrueml\MatomoWidgets\Parameter\ParameterResolverInterface", "stdClass" given');

        /** @noinspection PhpParamsInspection */
        $this->subject->set('foo', new \stdClass());
    }

    /**
     * @test
     */
    public function getThrowsExceptionWhenParameterIsNotFound(): void
    {
        $this->expectException(ParameterNotFoundException::class);
        $this->expectExceptionCode(1594369628);
        $this->expectExceptionMessage('Parameter "invalid" not found');

        $this->subject->get('invalid');
    }

    /**
     * @test
     */
    public function buildQueryReturnsQueryStringFromParameters(): void
    {
        $this->subject
            ->set('foo', 'bar')
            ->set('bar', 'qux')
            ->set('quu', 42);

        $actual = $this->subject->buildQuery();

        self::assertSame('foo=bar&bar=qux&quu=42', $actual);
    }
}
