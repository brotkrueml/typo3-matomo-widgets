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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\EventDispatcher\NoopEventDispatcher;

#[CoversClass(MatomoRepository::class)]
final class MatomoRepositoryTest extends TestCase
{
    private MatomoConnector&MockObject $connectorMock;
    private FrontendInterface&Stub $cacheStub;
    private ConnectionConfiguration $connectionConfiguration;
    private MatomoRepository $subject;

    protected function setUp(): void
    {
        $this->connectorMock = $this->createMock(MatomoConnector::class);
        $this->cacheStub = self::createStub(FrontendInterface::class);
        $eventDispatcher = new NoopEventDispatcher();

        $this->subject = new MatomoRepository(
            $this->cacheStub,
            new CacheIdentifierCreator(),
            $this->connectorMock,
            $eventDispatcher,
        );

        $this->connectionConfiguration = new ConnectionConfiguration('https://example.net', 3, '');
    }

    #[Test]
    public function sendWithCacheDisabledCallsConnectorCorrectly(): void
    {
        $parameterBag = new ParameterBag();

        $this->connectorMock
            ->expects(self::once())
            ->method('callApi')
            ->with($this->connectionConfiguration, 'some.method', $parameterBag);

        $this->subject->send($this->connectionConfiguration, 'some.method', $parameterBag, false);
    }

    #[Test]
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

    #[Test]
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
