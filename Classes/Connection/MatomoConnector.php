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
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
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
        $this->idSite = $configuration['idSite'];
        $this->tokenAuth = $configuration['tokenAuth'] ?: 'anonymous';
        $this->url = $configuration['url'];
    }

    public function callApi(string $method, ParameterBag $parameterBag): array
    {
        $this->checkConfiguration();

        $parameterBag
            ->set('module', 'API')
            ->set('idSite', $this->idSite)
            ->set('method', $method)
            ->set('token_auth', $this->tokenAuth)
            ->set('format', 'json');

        $body = new Stream('php://temp', 'r+');
        $body->write($parameterBag->buildQuery());

        $request = $this->requestFactory->createRequest('POST', $this->url)
            ->withHeader('content-type', 'application/x-www-form-urlencoded')
            ->withBody($body);
        $response = $this->client->sendRequest($request);

        $content = $response->getBody()->getContents();
        $this->checkResponseForErrors($content);

        return \json_decode($content, true);
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

    private function checkConfiguration(): void
    {
        if (!\is_numeric($this->idSite) || (int)$this->idSite <= 0) {
            throw new InvalidSiteIdException(
                \sprintf(
                    'idSite must be a positive integer, "%s" given. Please check your Matomo settings in the extension configuration.',
                    $this->idSite
                ),
                1593879284
            );
        }

        if (!$this->isValidUrl()) {
            throw new InvalidUrlException(
                \sprintf('The given URL "%s" is not valid. Please check your Matomo settings in the extension configuration.', $this->url),
                1593880003
            );
        }
    }

    private function isValidUrl(): bool
    {
        return \filter_var($this->url, \FILTER_VALIDATE_URL) !== false;
    }
}
