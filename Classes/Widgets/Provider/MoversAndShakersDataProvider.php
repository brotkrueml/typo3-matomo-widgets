<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Widgets\Provider;

use Brotkrueml\MatomoWidgets\Connection\ConnectionConfiguration;
use Brotkrueml\MatomoWidgets\Domain\Repository\MatomoRepository;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use Brotkrueml\MatomoWidgets\Parameter\PeriodResolverInterface;
use Brotkrueml\MatomoWidgets\Widgets\Decorator\DecoratorInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

/**
 * @internal
 * @phpstan-type Column array{column: string, header?: string, decorator?: DecoratorInterface, classes?: string}
 */
final class MoversAndShakersDataProvider
{
    /**
     * @param array<string, string> $parameters
     */
    public function __construct(
        private readonly MatomoRepository $repository,
        private readonly ConnectionConfiguration $connectionConfiguration,
        private readonly PeriodResolverInterface $periodResolver,
        private readonly string $method,
        private readonly array $parameters,
    ) {}

    /**
     * @return array<string, list<array<string, string|int|bool>>>
     */
    public function getRows(): array
    {
        $userLanguage = $this->getBackendUser()->user['lang'] ?? 'en';
        $parameters = \array_merge(
            $this->parameters,
            // We pass the language, so we have translated area labels/countries
            [
                'language' => $userLanguage === 'default' ? 'en' : $userLanguage,
            ],
        );

        return $this->repository->send($this->connectionConfiguration, $this->method, new ParameterBag($parameters));
    }

    public function getDatePeriod(): string
    {
        return $this->periodResolver->resolve($this->parameters['period'] ?? '', $this->parameters['date'] ?? '');
    }

    private function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
