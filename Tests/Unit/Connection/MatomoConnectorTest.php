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
use Brotkrueml\MatomoWidgets\Exception\ConnectionException;
use Brotkrueml\MatomoWidgets\Exception\InvalidResponseException;
use Brotkrueml\MatomoWidgets\Exception\InvalidSiteIdException;
use Brotkrueml\MatomoWidgets\Exception\InvalidUrlException;
use Brotkrueml\MatomoWidgets\Extension;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use donatj\MockWebServer\MockWebServer;
use donatj\MockWebServer\Response;
use GuzzleHttp\Client as GuzzleClient;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\Client;
use TYPO3\CMS\Core\Http\RequestFactory;

class MatomoConnectorTest extends TestCase
{
    /** @var MockWebServer */
    private static $server;

    /** @var Stub|ExtensionConfiguration */
    private $extensionConfigurationStub;

    /** @var RequestFactoryInterface */
    private $requestFactory;

    /** @var ClientInterface */
    private $client;

    public static function setUpBeforeClass(): void
    {
        self::$server = new MockWebServer();
        self::$server->start();
    }

    public static function tearDownAfterClass(): void
    {
        self::$server->stop();
    }

    protected function setUp(): void
    {
        $this->extensionConfigurationStub = $this->createStub(ExtensionConfiguration::class);
        $this->requestFactory = new RequestFactory();
        $this->client = new Client(new GuzzleClient());
    }

    /**
     * @test
     * @dataProvider dataProviderForCallApi
     * @param array $configuration
     * @param string $method
     * @param array $parameters
     * @param string $expectedQuery
     * @param string $expectedResult
     */
    public function callApi(
        array $configuration,
        string $method,
        array $parameters,
        string $expectedQuery,
        string $expectedResult
    ): void {
        $configuration['url'] = \sprintf('http://%s:%s/', self::$server->getHost(), self::$server->getPort());

        $this->extensionConfigurationStub
            ->method('get')
            ->with(Extension::KEY)
            ->willReturn($configuration);

        self::$server->setResponseOfPath(
            '/',
            new Response(
                $expectedResult,
                ['content-type' => 'application/json; charset=utf-8'],
                200
            )
        );

        $parameterBag = new ParameterBag();
        foreach ($parameters as $name => $value) {
            $parameterBag->set($name, $value);
        }

        $subject = new MatomoConnector($this->extensionConfigurationStub, $this->requestFactory, $this->client);
        $actual = $subject->callApi($method, $parameterBag);

        $lastRequest = self::$server->getLastRequest();
        self::assertSame('application/x-www-form-urlencoded', $lastRequest->getHeaders()['content-type']);
        self::assertSame('POST', $lastRequest->getRequestMethod());
        self::assertSame('/', $lastRequest->getRequestUri());
        $body = \http_build_query($lastRequest->getPost());
        self::assertSame($expectedQuery, $body);
        self::assertSame($expectedResult, \json_encode($actual));
    }

    public function dataProviderForCallApi(): \Generator
    {
        yield 'with parameters and no token' => [
            [
                'idSite' => '62',
                'tokenAuth' => '',
            ],
            'VisitsSummary.get',
            [
                'period' => 'day',
                'date' => 'today',
            ],
            'period=day&date=today&module=API&idSite=62&method=VisitsSummary.get&token_auth=anonymous&format=json',
            '{"nb_uniq_visitors":1518,"nb_users":0,"nb_visits":1579,"nb_actions":3102,"nb_visits_converted":126,"bounce_count":1063,"sum_visit_length":259992,"max_actions":44,"bounce_rate":"67%","nb_actions_per_visit":2,"avg_time_on_site":165}',
        ];

        yield 'without parameters and no token' => [
            [
                'idSite' => '62',
                'tokenAuth' => '',
            ],
            'API.getMatomoVersion',
            [],
            'module=API&idSite=62&method=API.getMatomoVersion&token_auth=anonymous&format=json',
            '{"value":"3.13.6"}',
        ];

        yield 'without parameters and token' => [
            [
                'idSite' => '62',
                'tokenAuth' => 'thesecrettoken',
            ],
            'API.getMatomoVersion',
            [],
            'module=API&idSite=62&method=API.getMatomoVersion&token_auth=thesecrettoken&format=json',
            '{"value":"3.13.6"}',
        ];

        yield 'with parameters having special characters being urlencoded' => [
            [
                'idSite' => '62',
                'tokenAuth' => 'thesecrettoken',
            ],
            'VisitsSummary.get',
            [
                'fo&o' => 'ba+r',
                'qu x' => 'qo"o',
            ],
            'fo%26o=ba%2Br&qu_x=qo%22o&module=API&idSite=62&method=VisitsSummary.get&token_auth=thesecrettoken&format=json',
            '{"nb_uniq_visitors":1518,"nb_users":0,"nb_visits":1579,"nb_actions":3102,"nb_visits_converted":126,"bounce_count":1063,"sum_visit_length":259992,"max_actions":44,"bounce_rate":"67%","nb_actions_per_visit":2,"avg_time_on_site":165}',
        ];
    }

    /**
     * @test
     */
    public function callApiThrowsExceptionWhenResponseIsNotValidJson(): void
    {
        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionCode(1595862844);
        $this->expectExceptionMessage('Content returned from Matomo Reporting API is not JSON encoded: No JSON');

        $configuration = [
            'idSite' => '62',
            'tokenAuth' => 'thesecrettoken',
            'url' => \sprintf('http://%s:%s/', self::$server->getHost(), self::$server->getPort()),
        ];

        $this->extensionConfigurationStub
            ->method('get')
            ->with(Extension::KEY)
            ->willReturn($configuration);

        self::$server->setResponseOfPath(
            '/',
            new Response(
                'No JSON',
                ['content-type' => 'application/json; charset=utf-8'],
                200
            )
        );

        $subject = new MatomoConnector($this->extensionConfigurationStub, $this->requestFactory, $this->client);
        $subject->callApi('some.method', new ParameterBag());
    }

    /**
     * @test
     */
    public function usingNotNumericIdSiteThrowsException(): void
    {
        $this->expectException(InvalidSiteIdException::class);
        $this->expectExceptionCode(1593879284);
        $this->expectExceptionMessage('idSite must be a positive integer, "foo" given. Please check your Matomo settings in the extension configuration.');

        $this->extensionConfigurationStub
            ->method('get')
            ->with(Extension::KEY)
            ->willReturn([
                'idSite' => 'foo',
                'tokenAuth' => 'thesecrettoken',
                'url' => 'https://example.org/',
            ]);

        $subject = new MatomoConnector($this->extensionConfigurationStub, $this->requestFactory, $this->client);
        $subject->callApi('foo.bar', new ParameterBag());
    }

    /**
     * @test
     */
    public function usingInvalidIdSiteThrowsException(): void
    {
        $this->expectException(InvalidSiteIdException::class);
        $this->expectExceptionCode(1593879284);
        $this->expectExceptionMessage('idSite must be a positive integer, "0" given. Please check your Matomo settings in the extension configuration.');

        $this->extensionConfigurationStub
            ->method('get')
            ->with(Extension::KEY)
            ->willReturn([
                'idSite' => '0',
                'tokenAuth' => 'thesecrettoken',
                'url' => 'https://example.org/',
            ]);

        $subject = new MatomoConnector($this->extensionConfigurationStub, $this->requestFactory, $this->client);
        $subject->callApi('foo.bar', new ParameterBag());
    }

    /**
     * @test
     */
    public function usingInvalidUrlThrowsException(): void
    {
        $this->expectException(InvalidUrlException::class);
        $this->expectExceptionCode(1593880003);
        $this->expectExceptionMessage('The given URL "invalid" is not valid. Please check your Matomo settings in the extension configuration.');

        $this->extensionConfigurationStub
            ->method('get')
            ->with(Extension::KEY)
            ->willReturn([
                'idSite' => '1',
                'tokenAuth' => 'thesecrettoken',
                'url' => 'invalid',
            ]);

        $subject = new MatomoConnector($this->extensionConfigurationStub, $this->requestFactory, $this->client);
        $subject->callApi('foo.bar', new ParameterBag());
    }

    /**
     * @test
     */
    public function whenErrorAsStringOccursAnExceptionIsThrown(): void
    {
        $this->expectException(ConnectionException::class);
        $this->expectExceptionCode(1593955897);
        $this->expectExceptionMessage('Error: Renderer format \'json1\' not valid. Try any of the following instead: console, csv, html, json, json2, original, php, rss, tsv, xml.');

        $configuration = [
            'idSite' => '62',
            'tokenAuth' => 'thesecrettoken',
            'url' => \sprintf('http://%s:%s/', self::$server->getHost(), self::$server->getPort()),
        ];

        $this->extensionConfigurationStub
            ->method('get')
            ->with(Extension::KEY)
            ->willReturn($configuration);

        self::$server->setResponseOfPath(
            '/',
            new Response(
                'Error: Renderer format \'json1\' not valid. Try any of the following instead: console, csv, html, json, json2, original, php, rss, tsv, xml.',
                [],
                200
            )
        );

        $subject = new MatomoConnector($this->extensionConfigurationStub, $this->requestFactory, $this->client);
        $subject->callApi('someMethod', new ParameterBag());
    }

    /**
     * @test
     */
    public function whenErrorAsJsonOccursAnExceptionIsThrown(): void
    {
        $this->expectException(ConnectionException::class);
        $this->expectExceptionCode(1593955989);
        $this->expectExceptionMessage('The method \'someMethod\' does not exist or is not available in the module');

        $configuration = [
            'idSite' => '62',
            'tokenAuth' => 'thesecrettoken',
            'url' => \sprintf('http://%s:%s/', self::$server->getHost(), self::$server->getPort()),
        ];

        $this->extensionConfigurationStub
            ->method('get')
            ->with(Extension::KEY)
            ->willReturn($configuration);

        self::$server->setResponseOfPath(
            '/',
            new Response(
                '{"result":"error","message":"The method \'someMethod\' does not exist or is not available in the module"}',
                [],
                200
            )
        );

        $subject = new MatomoConnector($this->extensionConfigurationStub, $this->requestFactory, $this->client);
        $subject->callApi('someMethod', new ParameterBag());
    }
}
