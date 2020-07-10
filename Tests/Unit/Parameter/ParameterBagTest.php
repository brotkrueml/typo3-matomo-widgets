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
        $subject = new ParameterBag(['foo' => 'bar', 'qux' => 'quu']);

        self::assertSame('bar', $subject->get('foo'));
        self::assertSame('quu', $subject->get('qux'));
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
    public function setOnceAndGetReturnsSetValue(): void
    {
        $this->subject->set('foo', 'bar');

        self::assertSame('bar', $this->subject->get('foo'));
    }

    /**
     * @test
     */
    public function setTwiceAndGetReturnsLastSetValue(): void
    {
        $this->subject->set('foo', 'bar');
        $this->subject->set('foo', 'qux');

        self::assertSame('qux', $this->subject->get('foo'));
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
            ->set('quu', 'foobar');

        $actual = $this->subject->buildQuery();

        self::assertSame('foo=bar&bar=qux&quu=foobar', $actual);
    }
}
