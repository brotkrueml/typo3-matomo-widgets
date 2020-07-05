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
use Brotkrueml\MatomoWidgets\Exception\InvalidSiteIdException;
use Brotkrueml\MatomoWidgets\Exception\InvalidUrlException;
use Brotkrueml\MatomoWidgets\Extension;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\Stream;

class MatomoConnector
{
    /** @var ExtensionConfiguration */
    private $extensionConfiguration;

    /** @var RequestFactoryInterface */
    private $requestFactory;

    /** @var ClientInterface */
    private $client;

    /** @var int */
    private $idSite;

    /** @var string */
    private $tokenAuth;

    /** @var string */
    private $url;

    public function __construct(
        ExtensionConfiguration $extensionConfiguration,
        RequestFactoryInterface $requestFactory,
        ClientInterface $client
    ) {
        $this->extensionConfiguration = $extensionConfiguration;
        $this->requestFactory = $requestFactory;
        $this->client = $client;

        $configuration = $this->extensionConfiguration->get(Extension::KEY);
        $this->idSite = (int)$configuration['idSite'];
        $this->tokenAuth = $configuration['tokenAuth'];
        $this->url = $configuration['url'];

        $this->checkConfiguration();
        $this->normaliseUrl();
    }

    private function checkConfiguration(): void
    {
        if ($this->idSite <= 0) {
            throw new InvalidSiteIdException(
                \sprintf('idSite must be a positive integer, "%d" given', $this->idSite),
                1593879284
            );
        }

        if (!$this->isValidUrl()) {
            throw new InvalidUrlException(
                \sprintf('The given URL "%s" is not valid', $this->url),
                1593880003
            );
        }
    }

    private function isValidUrl(): bool
    {
        return \filter_var($this->url, \FILTER_VALIDATE_URL) !== false;
    }

    private function normaliseUrl(): void
    {
        $this->url = \rtrim($this->url, '/') . '/';
    }

    public function callApi(string $method, array $parameters): array
    {
        $defaultParameters = [
            'module' => 'API',
            'idSite' => $this->idSite,
            'method' => $method,
            'format' => 'json',
        ];
        if ($this->tokenAuth) {
            // Parameter is optional
            $defaultParameters['token_auth'] = $this->tokenAuth;
        }

        $query = \http_build_query(\array_merge($defaultParameters, $parameters));

        $body = new Stream('php://temp', 'r+');
        $body->write($query);

        $request = $this->requestFactory->createRequest('POST', $this->url)
            ->withHeader('content-type', 'application/x-www-form-urlencoded')
            ->withBody($body);
        $response = $this->client->sendRequest($request);

        $content = $response->getBody()->getContents();
        $this->checkResponseForError($content);

        return \json_decode($content, true);
    }

    private function checkResponseForError(string $content): void
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
