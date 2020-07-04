<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Connection;

use Brotkrueml\MatomoWidgets\Exception\InvalidSiteIdException;
use Brotkrueml\MatomoWidgets\Exception\InvalidUrlException;
use Brotkrueml\MatomoWidgets\Extension;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

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
        $additionalQueryString = '';
        foreach ($parameters as $name => $value) {
            $additionalQueryString .= \sprintf('&%s=%s', \urlencode($name), \urlencode($value));
        }

        $apiUrl = \sprintf(
            '%s?module=API&idSite=%d&token_auth=%s&method=%s&format=json%s',
            $this->url,
            $this->idSite,
            \urlencode($this->tokenAuth),
            $method,
            $additionalQueryString
        );

        $request = $this->requestFactory->createRequest('GET', $apiUrl);
        $response = $this->client->sendRequest($request);

        return \json_decode($response->getBody()->getContents(), true);
    }
}
