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
use Psr\Http\Message\RequestFactoryInterface;
use TYPO3\CMS\Core\Http\Client\GuzzleClientFactory;
use TYPO3\CMS\Core\Http\Stream;

/**
 * @internal
 */
class MatomoConnector
{
    public function __construct(
        private readonly RequestFactoryInterface $requestFactory,
        private readonly GuzzleClientFactory $guzzleClientFactory,
    ) {}

    public function callApi(ConnectionConfiguration $configuration, string $method, ParameterBag $parameterBag): array
    {
        $parameterBag
            ->set('module', 'API')
            ->set('idSite', (string) $configuration->idSite)
            ->set('method', $method)
            ->set('token_auth', $configuration->tokenAuth)
            ->set('format', 'json');

        $body = new Stream('php://temp', 'r+');
        $body->write($parameterBag->buildQuery());

        $request = $this->requestFactory->createRequest('POST', $configuration->url)
            ->withHeader('content-type', 'application/x-www-form-urlencoded')
            ->withBody($body);

        $response = $this->guzzleClientFactory->getClient()->send($request);

        return $this->checkResponse($response->getBody()->getContents());
    }

    /**
     * @return array<int|string, mixed>
     */
    private function checkResponse(string $content): array
    {
        if (\str_starts_with($content, 'Error')) {
            throw new ConnectionException($content, 1593955897);
        }

        try {
            $decoded = \json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InvalidResponseException(
                \sprintf('Content returned from Matomo Reporting API is not JSON encoded: %s', $content),
                1595862844,
                $e,
            );
        }

        if (! isset($decoded['result'])) {
            return $decoded;
        }
        if ($decoded['result'] !== 'error') {
            return $decoded;
        }

        throw new ConnectionException($decoded['message'], 1593955989);
    }
}
