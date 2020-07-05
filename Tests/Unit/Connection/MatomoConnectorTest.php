<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Connection;

use Brotkrueml\MatomoWidgets\Connection\MatomoConnector;
use Brotkrueml\MatomoWidgets\Exception\InvalidSiteIdException;
use Brotkrueml\MatomoWidgets\Exception\InvalidUrlException;
use Brotkrueml\MatomoWidgets\Extension;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class MatomoConnectorTest extends TestCase
{
    /** @var Stub|ExtensionConfiguration */
    private $extensionConfigurationStub;

    /** @var Stub|RequestFactoryInterface */
    private $requestFactoryStub;

    /** @var Stub|ClientInterface */
    private $clientStub;

    /** @var MockObject|RequestInterface */
    private $requestMock;

    /** @var Stub|ResponseInterface */
    private $responseStub;

    protected function setUp(): void
    {
        $this->extensionConfigurationStub = $this->createStub(ExtensionConfiguration::class);
        $this->requestFactoryStub = $this->createStub(RequestFactoryInterface::class);
        $this->clientStub = $this->createStub(ClientInterface::class);
        $this->requestMock = $this->createMock(RequestInterface::class);
        $this->responseStub = $this->createStub(ResponseInterface::class);
    }

    /**
     * @test
     * @dataProvider dataProviderForCallApi
     * @param array $configuration
     * @param array $parameters
     * @param string $expected
     */
    public function callApiIsImplementedCorrectly(array $configuration, array $parameters, string $expectedQuery): void
    {
        $this->extensionConfigurationStub
            ->method('get')
            ->with(Extension::KEY)
            ->willReturn($configuration);

        $subject = new MatomoConnector($this->extensionConfigurationStub, $this->requestFactoryStub, $this->clientStub);

        $this->requestMock
            ->expects(self::at(0))
            ->method('withHeader')
            ->with('content-type', 'application/x-www-form-urlencoded')
            ->willReturn($this->requestMock);

        $this->requestMock
            ->expects(self::at(1))
            ->method('withBody')
            ->willReturn($this->requestMock);

        $this->requestFactoryStub
            ->method('createRequest')
            ->with('POST', 'https://example.org/')
            ->willReturn($this->requestMock);

        $streamStub = $this->createStub(StreamInterface::class);
        $streamStub
            ->method('getContents')
            ->willReturn('{"some": "result"}');

        $this->responseStub
            ->method('getBody')
            ->willReturn($streamStub);

        $this->clientStub
            ->method('sendRequest')
            ->with($this->requestMock)
            ->willReturn($this->responseStub);

        self::assertSame(['some' => 'result'], $subject->callApi('SomeModule.someMethod', $parameters));
    }

    public function dataProviderForCallApi(): \Generator
    {
        yield 'without parameters' => [
            [
                'idSite' => '42',
                'tokenAuth' => 'thesecrettoken',
                'url' => 'https://example.org/',
            ],
            [],
            'module=API&idSite=42&token_auth=thesecrettoken&method=SomeModule.someMethod&format=json'
        ];

        yield 'with parameters' => [
            [
                'idSite' => '42',
                'tokenAuth' => 'thesecrettoken',
                'url' => 'https://example.org/',
            ],
            [
                'foo' => 'bar',
                'qux' => 'qoo',
            ],
            'module=API&idSite=42&token_auth=thesecrettoken&method=SomeModule.someMethod&format=json&foo=bar&qux=qoo'
        ];

        yield 'with parameters having special characters being urlencoded' => [
            [
                'idSite' => '42',
                'tokenAuth' => 'thesecrettoken',
                'url' => 'https://example.org/',
            ],
            [
                'fo&o' => 'ba+r',
                'qu x' => 'qo"o',
            ],
            'module=API&idSite=42&token_auth=thesecrettoken&method=SomeModule.someMethod&format=json&fo%26o=ba%2Br&qu+x=qo%22o'
        ];

        yield 'with auth token having special characters being urlencoded' => [
            [
                'idSite' => '42',
                'tokenAuth' => 'the secret token',
                'url' => 'https://example.org/',
            ],
            [],
            'module=API&idSite=42&token_auth=the+secret+token&method=SomeModule.someMethod&format=json'
        ];

        yield '/ is appended to url if missing' => [
            [
                'idSite' => '42',
                'tokenAuth' => 'the secret token',
                'url' => 'https://example.org',
            ],
            [],
            'module=API&idSite=42&token_auth=the+secret+token&method=SomeModule.someMethod&format=json'
        ];
    }

    /**
     * @test
     */
    public function usingInvalidIdSiteThrowsException(): void
    {
        $this->expectException(InvalidSiteIdException::class);
        $this->expectExceptionCode(1593879284);
        $this->expectExceptionMessage('idSite must be a positive integer, "0" given');

        $this->extensionConfigurationStub
            ->method('get')
            ->with(Extension::KEY)
            ->willReturn([
                'idSite' => '0',
                'tokenAuth' => 'thesecrettoken',
                'url' => 'https://example.org/',
            ]);

        new MatomoConnector($this->extensionConfigurationStub, $this->requestFactoryStub, $this->clientStub);
    }

    /**
     * @test
     */
    public function usingInvalidUrlThrowsException(): void
    {
        $this->expectException(InvalidUrlException::class);
        $this->expectExceptionCode(1593880003);
        $this->expectExceptionMessage('The given URL "invalid" is not valid');

        $this->extensionConfigurationStub
            ->method('get')
            ->with(Extension::KEY)
            ->willReturn([
                'idSite' => '1',
                'tokenAuth' => 'thesecrettoken',
                'url' => 'invalid',
            ]);

        new MatomoConnector($this->extensionConfigurationStub, $this->requestFactoryStub, $this->clientStub);
    }
}
