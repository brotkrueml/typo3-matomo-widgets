<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Controller;

use Brotkrueml\MatomoWidgets\Configuration\Configuration;
use Brotkrueml\MatomoWidgets\Configuration\ConfigurationFinder;
use Brotkrueml\MatomoWidgets\Connection\ConnectionConfiguration;
use Brotkrueml\MatomoWidgets\Domain\Aggregation\JavaScriptErrorDetailsAggregator;
use Brotkrueml\MatomoWidgets\Domain\Repository\MatomoRepository;
use Brotkrueml\MatomoWidgets\Exception\SiteConfigurationNotFoundException;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * @internal
 */
final class JavaScriptErrorDetailsController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var ConfigurationFinder
     */
    private $configurationFinder;
    /**
     * @var JavaScriptErrorDetailsAggregator
     */
    private $aggregator;
    /**
     * @var MatomoRepository
     */
    private $repository;
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;
    /**
     * @var StandaloneView
     */
    private $view;
    /**
     * @var array<string, string|int>
     */
    private $parameters;

    /**
     * @param array{period: string, date: string} $parameters
     */
    public function __construct(
        ConfigurationFinder $configurationFinder,
        JavaScriptErrorDetailsAggregator $aggregator,
        MatomoRepository $repository,
        ResponseFactoryInterface $responseFactory,
        StandaloneView $view,
        array $parameters
    ) {
        $this->configurationFinder = $configurationFinder;
        $this->aggregator = $aggregator;
        $this->repository = $repository;
        $this->responseFactory = $responseFactory;
        $this->view = $view;
        $this->parameters = $parameters;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $this->checkQueryParams($queryParams);
        $siteIdentifier = $queryParams['siteIdentifier'];
        $errorMessage = $queryParams['errorMessage'];

        $siteConfiguration = null;

        foreach ($this->configurationFinder as $configuration) {
            /** @var Configuration $configuration */
            if ($configuration->getSiteIdentifier() === $siteIdentifier) {
                $siteConfiguration = $configuration;
                break;
            }
        }

        if ($siteConfiguration === null) {
            throw new SiteConfigurationNotFoundException('Site configuration not found!');
        }

        $connectionConfiguration = new ConnectionConfiguration(
            $siteConfiguration->getUrl(),
            $siteConfiguration->getIdSite(),
            $siteConfiguration->getTokenAuth()
        );

        $parameterBag = new ParameterBag([
            'period' => $this->parameters['period'],
            'date' => $this->parameters['date'],
            'segment' => 'eventName==' . $errorMessage,
            'showColumns' => 'actionDetails,browserName,browserIcon',
        ]);

        try {
            $visits = $this->repository->send($connectionConfiguration, 'Live.getLastVisitsDetails', $parameterBag);
        } catch (\Throwable $t) {
            $this->logger->error($t->getMessage());
            $response = $this->responseFactory->createResponse()
                ->withHeader('Content-Type', 'text/plain; charset=utf-8');
            $response->getBody()->write('An error occurred, please have a look into the TYPO3 log file for details.');

            return $response;
        }
        $details = $this->aggregator->aggregate($visits);

        $this->view->setTemplatePathAndFilename('EXT:matomo_widgets/Resources/Private/Templates/JavaScriptErrorDetails.html');
        $this->view->assignMultiple([
            'matomoBaseUrl' => $siteConfiguration->getUrl(),
            'details' => $details,
        ]);

        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'text/html; charset=utf-8');
        $response->getBody()->write($this->view->render());

        return $response;
    }

    /**
     * @param array{siteIdentifier?: string, errorMessage?: string} $queryParams
     */
    private function checkQueryParams(array $queryParams): void
    {
        if (! isset($queryParams['siteIdentifier'])) {
            throw new \InvalidArgumentException('Site identifier is not given!', 1643618580);
        }

        if (! isset($queryParams['errorMessage'])) {
            throw new \InvalidArgumentException('Error message is not given!', 1643618581);
        }
    }
}
