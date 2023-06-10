<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Connection;

use Brotkrueml\MatomoWidgets\Adapter\GuzzleClientFactory;
use Brotkrueml\MatomoWidgets\Connection\ConnectionConfiguration;
use Brotkrueml\MatomoWidgets\Connection\MatomoConnector;
use Brotkrueml\MatomoWidgets\Exception\ConnectionException;
use Brotkrueml\MatomoWidgets\Exception\InvalidResponseException;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use donatj\MockWebServer\MockWebServer;
use donatj\MockWebServer\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use TYPO3\CMS\Core\Http\Client;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Information\Typo3Version;

#[CoversClass(MatomoConnector::class)]
final class MatomoConnectorTest extends TestCase
{
    private static MockWebServer $server;
    private RequestFactoryInterface $requestFactory;
    private ClientInterface $client;
    private string $url;

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
        $GLOBALS['TYPO3_CONF_VARS']['HTTP']['verify'] = false;

        $isTypo3Version12 = (new Typo3Version())->getMajorVersion() === 12;
        if ($isTypo3Version12) {
            $this->requestFactory = new RequestFactory(new Client\GuzzleClientFactory());
        } else {
            $this->requestFactory = new RequestFactory();
        }

        $this->client = (new GuzzleClientFactory())->getClient();

        $this->guzzleClientFactoryStub = $this->createStub(GuzzleClientFactory::class);
        $this->guzzleClientFactoryStub
            ->method('getClient')
            ->willReturn($this->client);

        $this->url = \sprintf('http://%s:%s/', self::$server->getHost(), self::$server->getPort());
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_CONF_VARS']['HTTP']['verify']);
    }

    #[Test]
    #[DataProvider('dataProviderForCallApi')]
    public function callApi(
        array $configuration,
        string $method,
        array $parameters,
        string $expectedQuery,
        string $expectedResult,
    ): void {
        $connectionConfiguration = new ConnectionConfiguration($this->url, $configuration['idSite'], $configuration['tokenAuth']);

        self::$server->setResponseOfPath(
            '/',
            new Response(
                $expectedResult,
                [
                    'content-type' => 'application/json; charset=utf-8',
                ],
                200,
            ),
        );

        $parameterBag = new ParameterBag();
        foreach ($parameters as $name => $value) {
            $parameterBag->set($name, $value);
        }

        $subject = new MatomoConnector($this->requestFactory, new GuzzleClientFactory());
        $actual = $subject->callApi($connectionConfiguration, $method, $parameterBag);

        $lastRequest = self::$server->getLastRequest();
        self::assertSame('application/x-www-form-urlencoded', $lastRequest->getHeaders()['content-type']);
        self::assertSame('POST', $lastRequest->getRequestMethod());
        self::assertSame('/', $lastRequest->getRequestUri());
        $body = \http_build_query($lastRequest->getPost());
        self::assertSame($expectedQuery, $body);
        self::assertSame($expectedResult, \json_encode($actual, \JSON_THROW_ON_ERROR));
    }

    public static function dataProviderForCallApi(): iterable
    {
        yield 'with parameters and no token' => [
            [
                'idSite' => 62,
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
                'idSite' => 62,
                'tokenAuth' => '',
            ],
            'API.getMatomoVersion',
            [],
            'module=API&idSite=62&method=API.getMatomoVersion&token_auth=anonymous&format=json',
            '{"value":"3.13.6"}',
        ];

        yield 'without parameters and token' => [
            [
                'idSite' => 62,
                'tokenAuth' => 'thesecrettoken',
            ],
            'API.getMatomoVersion',
            [],
            'module=API&idSite=62&method=API.getMatomoVersion&token_auth=thesecrettoken&format=json',
            '{"value":"3.13.6"}',
        ];

        yield 'with parameters having special characters being urlencoded' => [
            [
                'idSite' => 62,
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

    #[Test]
    public function callApiThrowsExceptionWhenResponseIsNotValidJson(): void
    {
        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionCode(1595862844);
        $this->expectExceptionMessage('Content returned from Matomo Reporting API is not JSON encoded: No JSON');

        $connectionConfiguration = new ConnectionConfiguration($this->url, 62, 'thesecrettoken');

        self::$server->setResponseOfPath(
            '/',
            new Response(
                'No JSON',
                [
                    'content-type' => 'application/json; charset=utf-8',
                ],
                200,
            ),
        );

        $subject = new MatomoConnector($this->requestFactory, new GuzzleClientFactory());
        $subject->callApi($connectionConfiguration, 'some.method', new ParameterBag());
    }

    #[Test]
    public function whenErrorAsStringOccursAnExceptionIsThrown(): void
    {
        $this->expectException(ConnectionException::class);
        $this->expectExceptionCode(1593955897);
        $this->expectExceptionMessage('Error: Renderer format \'json1\' not valid. Try any of the following instead: console, csv, html, json, json2, original, php, rss, tsv, xml.');

        $connectionConfiguration = new ConnectionConfiguration($this->url, 62, 'thesecrettoken');

        self::$server->setResponseOfPath(
            '/',
            new Response(
                'Error: Renderer format \'json1\' not valid. Try any of the following instead: console, csv, html, json, json2, original, php, rss, tsv, xml.',
                [],
                200,
            ),
        );

        $subject = new MatomoConnector($this->requestFactory, new GuzzleClientFactory());
        $subject->callApi($connectionConfiguration, 'someMethod', new ParameterBag());
    }

    #[Test]
    public function whenErrorAsJsonOccursAnExceptionIsThrown(): void
    {
        $this->expectException(ConnectionException::class);
        $this->expectExceptionCode(1593955989);
        $this->expectExceptionMessage('The method \'someMethod\' does not exist or is not available in the module');

        $connectionConfiguration = new ConnectionConfiguration($this->url, 62, 'thesecrettoken');

        self::$server->setResponseOfPath(
            '/',
            new Response(
                '{"result":"error","message":"The method \'someMethod\' does not exist or is not available in the module"}',
                [],
                200,
            ),
        );

        $subject = new MatomoConnector($this->requestFactory, new GuzzleClientFactory());
        $subject->callApi($connectionConfiguration, 'someMethod', new ParameterBag());
    }
}
