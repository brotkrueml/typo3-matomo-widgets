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
use Brotkrueml\MatomoWidgets\Configuration\Configurations;
use Brotkrueml\MatomoWidgets\Connection\ConnectionConfiguration;
use Brotkrueml\MatomoWidgets\Domain\Aggregation\JavaScriptErrorDetailsAggregator;
use Brotkrueml\MatomoWidgets\Domain\Repository\MatomoRepository;
use Brotkrueml\MatomoWidgets\Exception\SiteConfigurationNotFoundException;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * @internal
 */
final class JavaScriptErrorDetailsController
{
    /**
     * @param array{period: string, date: string} $parameters
     */
    public function __construct(
        private readonly Configurations $configurations,
        private readonly JavaScriptErrorDetailsAggregator $aggregator,
        private readonly LoggerInterface $logger,
        private readonly MatomoRepository $repository,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly StandaloneView $view,
        private readonly array $parameters,
    ) {}

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $this->checkQueryParams($queryParams);
        $siteIdentifier = $queryParams['siteIdentifier'];
        $errorMessage = $queryParams['errorMessage'];

        $siteConfiguration = $this->configurations->findConfigurationBySiteIdentifier($siteIdentifier);
        if (! $siteConfiguration instanceof Configuration) {
            throw new SiteConfigurationNotFoundException('Site configuration not found!');
        }

        $connectionConfiguration = new ConnectionConfiguration(
            $siteConfiguration->url,
            $siteConfiguration->idSite,
            $siteConfiguration->tokenAuth,
        );

        $parameterBag = new ParameterBag([
            'period' => $this->parameters['period'],
            'date' => $this->parameters['date'],
            'segment' => 'eventName==' . $errorMessage,
            'showColumns' => 'actionDetails,browserName,browserIcon,browserVersion',
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
            'matomoBaseUrl' => $siteConfiguration->url,
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
