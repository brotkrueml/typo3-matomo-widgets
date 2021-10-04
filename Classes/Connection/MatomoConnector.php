<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Connection;

use Brotkrueml\MatomoWidgets\Exception\ConnectionException;
use Brotkrueml\MatomoWidgets\Exception\InvalidResponseException;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use TYPO3\CMS\Core\Http\Stream;

class MatomoConnector
{
    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(RequestFactoryInterface $requestFactory, ClientInterface $client)
    {
        $this->requestFactory = $requestFactory;
        $this->client = $client;
    }

    public function callApi(ConnectionConfiguration $configuration, string $method, ParameterBag $parameterBag): array
    {
        $parameterBag
            ->set('module', 'API')
            ->set('idSite', (string)$configuration->getIdSite())
            ->set('method', $method)
            ->set('token_auth', $configuration->getTokenAuth())
            ->set('format', 'json');

        /** @psalm-suppress InternalClass,InternalMethod */
        $body = new Stream('php://temp', 'r+');
        /** @psalm-suppress InternalMethod */
        $body->write($parameterBag->buildQuery());

        $request = $this->requestFactory->createRequest('POST', $configuration->getUrl())
            ->withHeader('content-type', 'application/x-www-form-urlencoded')
            ->withBody($body);
        $response = $this->client->sendRequest($request);

        $content = $response->getBody()->getContents();
        $this->checkResponseForErrors($content);

        $result = \json_decode($content, true);
        if ($result === null) {
            throw new InvalidResponseException(
                \sprintf('Content returned from Matomo Reporting API is not JSON encoded: %s', $content),
                1595862844
            );
        }

        return $result;
    }

    private function checkResponseForErrors(string $content): void
    {
        if (\strpos($content, 'Error') === 0) {
            throw new ConnectionException($content, 1593955897);
        }

        $decoded = \json_decode($content, true);
        if (isset($decoded['result']) && $decoded['result'] === 'error') {
            throw new ConnectionException($decoded['message'], 1593955989);
        }
    }
}
