<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Domain\Repository;

use Brotkrueml\MatomoWidgets\Cache\CacheIdentifierCreator;
use Brotkrueml\MatomoWidgets\Connection\ConnectionConfiguration;
use Brotkrueml\MatomoWidgets\Connection\MatomoConnector;
use Brotkrueml\MatomoWidgets\Domain\Repository\MatomoRepository;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

class MatomoRepositoryTest extends TestCase
{
    /**
     * @var MatomoConnector|MockObject
     */
    private MockObject $connectorMock;
    /**
     * @var Stub|FrontendInterface
     */
    private Stub $cacheStub;
    private EventDispatcherInterface $eventDispatcherStub;
    private MatomoRepository $subject;
    private ConnectionConfiguration $connectionConfiguration;

    protected function setUp(): void
    {
        $this->connectorMock = $this->createMock(MatomoConnector::class);

        $this->cacheStub = $this->createStub(FrontendInterface::class);

        $this->eventDispatcherStub = new class() implements EventDispatcherInterface {
            public function dispatch(object $event)
            {
                return $event;
            }
        };

        $this->subject = new MatomoRepository(
            $this->cacheStub,
            new CacheIdentifierCreator(),
            $this->connectorMock,
            $this->eventDispatcherStub
        );

        $this->connectionConfiguration = new ConnectionConfiguration('https://example.net', 3, '');
    }

    /**
     * @test
     */
    public function sendWithCacheDisabledCallsConnectorCorrectly(): void
    {
        $parameterBag = new ParameterBag();

        $this->connectorMock
            ->expects(self::once())
            ->method('callApi')
            ->with($this->connectionConfiguration, 'some.method', $parameterBag);

        $this->subject->send($this->connectionConfiguration, 'some.method', $parameterBag, false);
    }

    /**
     * @test
     */
    public function sendWithCacheEnabledAndCacheEntryIsAvailableDoesNotCallConnector(): void
    {
        $parameterBag = new ParameterBag();

        $expected = [
            'some' => 'data',
        ];

        $this->cacheStub
            ->method('get')
            ->willReturn($expected);

        $this->connectorMock
            ->expects(self::never())
            ->method('callApi');

        $actual = $this->subject->send($this->connectionConfiguration, 'some.method', $parameterBag);

        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function sendWithCacheEnabledAndCacheEntryIsNotAvailableCallsConnector(): void
    {
        $parameterBag = new ParameterBag();

        $this->cacheStub
            ->method('get')
            ->willReturn(false);

        $this->connectorMock
            ->expects(self::once())
            ->method('callApi')
            ->with($this->connectionConfiguration, 'some.method', $parameterBag);

        $this->subject->send($this->connectionConfiguration, 'some.method', $parameterBag);
    }
}
