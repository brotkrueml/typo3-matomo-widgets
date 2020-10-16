<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Daomin\Repository;

use Brotkrueml\MatomoWidgets\Connection\ConnectionConfiguration;
use Brotkrueml\MatomoWidgets\Domain\Repository\CachingRepositoryDecorator;
use Brotkrueml\MatomoWidgets\Domain\Repository\RepositoryInterface;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

class CachingRepositoryDecoratorTest extends TestCase
{
    /**
     * @var RepositoryInterface|MockObject
     */
    private $repositoryMock;

    /**
     * @var MockObject|FrontendInterface
     */
    private $cacheMock;

    /**
     * @var ConnectionConfiguration
     */
    private $connectionConfiguration;

    /**
     * @var CachingRepositoryDecorator
     */
    private $subject;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(RepositoryInterface::class);
        $this->cacheMock = $this->createMock(FrontendInterface::class);
        $this->connectionConfiguration = new ConnectionConfiguration('https://example.com', 2, '');
        $this->subject = new CachingRepositoryDecorator($this->repositoryMock, $this->cacheMock);
    }

    /**
     * @test
     */
    public function findWithoutCacheHitReturnsData(): void
    {
        $method = 'some.method';
        $parameterBag = (new ParameterBag())->set('bar', 'quu');
        $data = ['abc' => 'def', 'ghi' => 'jkl'];

        $cacheIdentifier = 'some_method_' . \md5(\serialize($this->connectionConfiguration) . \serialize($parameterBag));

        $this->cacheMock
            ->expects(self::at(0))
            ->method('get')
            ->with($cacheIdentifier)
            ->willReturn(false);
        $this->cacheMock
            ->expects(self::at(1))
            ->method('set')
            ->with($cacheIdentifier, $data);

        $this->repositoryMock
            ->expects(self::once())
            ->method('find')
            ->with($this->connectionConfiguration, $method, $parameterBag)
            ->willReturn($data);

        self::assertSame($data, $this->subject->find($this->connectionConfiguration, $method, $parameterBag));
    }

    /**
     * @test
     */
    public function findWithCacheHitReturnsData(): void
    {
        $method = 'another.method';
        $parameterBag = (new ParameterBag())->set('qux', 'foo');
        $data = ['abc' => '123', 'def' => '456'];

        $this->cacheMock
            ->expects(self::once())
            ->method('get')
            ->willReturn($data);
        $this->cacheMock
            ->expects(self::never())
            ->method('set');

        $this->repositoryMock
            ->expects(self::never())
            ->method('find');

        self::assertSame($data, $this->subject->find($this->connectionConfiguration, $method, $parameterBag));
    }
}
