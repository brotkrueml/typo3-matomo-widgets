<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Controller;

use Brotkrueml\MatomoWidgets\Cache\CacheIdentifierCreator;
use Brotkrueml\MatomoWidgets\Configuration\Configuration;
use Brotkrueml\MatomoWidgets\Configuration\Configurations;
use Brotkrueml\MatomoWidgets\Connection\ConnectionConfiguration;
use Brotkrueml\MatomoWidgets\Domain\Repository\MatomoRepository;
use Brotkrueml\MatomoWidgets\Exception\SiteConfigurationNotFoundException;
use Brotkrueml\MatomoWidgets\Extension;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Localization\LanguageService;

/**
 * @internal
 */
final class CreateAnnotationController
{
    private string $siteIdentifier;
    private \DateTimeImmutable $date;
    private string $note;

    public function __construct(
        private readonly FrontendInterface $cache,
        private readonly CacheIdentifierCreator $cacheIdentifierCreator,
        private readonly Configurations $configurations,
        private readonly LoggerInterface $logger,
        private readonly MatomoRepository $repository,
        private readonly ResponseFactoryInterface $responseFactory
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $parameters = $request->getParsedBody();
        if (! \is_array($parameters)) {
            return $this->buildResponse(true, 'Given parameters in body cannot be converted to an array');
        }
        try {
            $this->checkParameters(
                (string)($parameters['siteIdentifier'] ?? ''),
                (string)($parameters['date'] ?? ''),
                (string)($parameters['note'] ?? '')
            );
        } catch (\InvalidArgumentException $e) {
            return $this->buildResponse(true, $e->getMessage());
        }

        if (! $this->hasUserPermissionForWidget()) {
            return $this->buildResponse(true, $this->translate('widgets.createAnnotation.error.noPermission'));
        }

        try {
            $this->createAnnotation();
        } catch (\Throwable $t) {
            $this->logger->error($t->getMessage());

            return $this->buildResponse(true, 'An error occurred, please have a look into the TYPO3 log file for details.');
        }

        return $this->buildResponse();
    }

    private function checkParameters(string $siteIdentifier, string $date, string $note): void
    {
        if ($siteIdentifier === '') {
            throw new \InvalidArgumentException($this->translate('error.emptySiteIdentifier'));
        }

        if ($date === '') {
            throw new \InvalidArgumentException($this->translate('error.emptyDate'));
        }

        if ($note === '') {
            throw new \InvalidArgumentException($this->translate('error.emptyNote'));
        }

        $this->siteIdentifier = $siteIdentifier;
        try {
            $this->date = new \DateTimeImmutable($date);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException($this->translate('error.invalidDate'), $e->getCode(), $e);
        }
        $this->note = $note;
    }

    private function translate(string $key): string
    {
        return $this->getLanguageService()->sL(
            \sprintf('%s:%s', Extension::LANGUAGE_PATH_DASHBOARD, $key)
        ) ?: $key;
    }

    private function buildResponse(bool $isError = false, string $message = ''): ResponseInterface
    {
        $data = [
            'status' => $isError ? 'error' : 'success',
        ];
        if ($message !== '') {
            $data['message'] = $message;
        }

        $response = $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'application/json; charset=utf-8');
        $response->getBody()->write(\json_encode($data, \JSON_THROW_ON_ERROR));

        return $response;
    }

    private function hasUserPermissionForWidget(): bool
    {
        return $this->getBackendUser()->check(
            'available_widgets',
            \sprintf(Extension::WIDGET_IDENTIFIER_TEMPLATE, $this->siteIdentifier, 'annotation.create')
        );
    }

    private function createAnnotation(): void
    {
        $siteConfiguration = $this->configurations->findConfigurationBySiteIdentifier($this->siteIdentifier);
        if (! $siteConfiguration instanceof Configuration) {
            throw new SiteConfigurationNotFoundException('Site configuration not found!');
        }

        $connectionConfiguration = new ConnectionConfiguration(
            $siteConfiguration->url,
            $siteConfiguration->idSite,
            $siteConfiguration->tokenAuth
        );

        $parameterBag = new ParameterBag([
            'date' => $this->date->format('Y-m-d'),
            'note' => $this->note,
        ]);

        /** @var array{date: string, note: string, idNote: string} $createdAnnotation */
        $createdAnnotation = $this->repository->send($connectionConfiguration, 'Annotations.add', $parameterBag, false);
        $this->log($createdAnnotation);
        $this->flushCache($connectionConfiguration);
    }

    /**
     * @param array{date: string, note: string, idNote: string} $createdAnnotation
     */
    private function log(array $createdAnnotation): void
    {
        // Usage of writelog() should be avoided, instead the new logging API should be used.
        // However, the DatabaseWriter logs into a completely new custom table for which TYPO3 provides no
        // backend module itself, which makes it harder for system maintainers to follow who created
        // a new annotation on the Matomo installation.
        // See: https://docs.typo3.org/m/typo3/reference-coreapi/10.4/en-us/ApiOverview/SystemLog/Index.html
        $this->getBackendUser()->writelog(
            4, // EXTENSION
            0,
            0,
            0,
            'Matomo Widgets: Annotation "%s / %s" (%d) was created on Matomo installation for site "%s"',
            [
                $createdAnnotation['date'],
                $createdAnnotation['note'],
                $createdAnnotation['idNote'],
                $this->siteIdentifier,
            ]
        );
    }

    private function flushCache(ConnectionConfiguration $configuration): void
    {
        $tag = $this->cacheIdentifierCreator->createTag($configuration, 'Annotations_getAll');
        $this->cache->flushByTag($tag);
    }

    private function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
