<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Widgets\Provider;

use Brotkrueml\MatomoWidgets\Domain\Repository\RepositoryInterface;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericValueDataProvider;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

class GenericValueDataProviderTest extends TestCase
{
    /**
     * @var Stub|RepositoryInterface
     */
    private $repositoryStub;

    /**
     * @var GenericValueDataProvider
     */
    private $subject;

    protected function setUp(): void
    {
        $this->repositoryStub = $this->createStub(RepositoryInterface::class);
        $this->subject = new GenericValueDataProvider(
            $this->repositoryStub,
            'some.method',
            'the_column',
            ['foo' => 'bar']
        );
    }

    /**
     * @test
     */
    public function getValueForExistingColumn(): void
    {
        $this->repositoryStub
            ->method('find')
            ->with('some.method', new ParameterBag(['foo' => 'bar']))
            ->willReturn(['the_column' => '123', 'another_column' => 987]);

        $actual = $this->subject->getValue();

        self::assertSame('123', $actual);
    }

    /**
     * @test
     */
    public function getValueForNonExistingColumn(): void
    {
        $this->repositoryStub
            ->method('find')
            ->with('some.method', new ParameterBag(['foo' => 'bar']))
            ->willReturn(['a_column' => 987]);

        $actual = $this->subject->getValue();

        self::assertSame('', $actual);
    }
}
