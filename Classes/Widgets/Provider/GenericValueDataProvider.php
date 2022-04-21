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
use Brotkrueml\MatomoWidgets\Domain\Repository\MatomoRepositoryInterface;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;

/**
 * @internal
 */
final class GenericValueDataProvider implements ValueDataProviderInterface
{
    private MatomoRepositoryInterface $repository;
    private ConnectionConfiguration $connectionConfiguration;
    private string $method;
    private string $columnName;
    /**
     * @var array<string, string>
     */
    private array $parameters;

    /**
     * @param array<string, string> $parameters
     */
    public function __construct(
        MatomoRepositoryInterface $repository,
        ConnectionConfiguration $connectionConfiguration,
        string $method,
        string $columnName,
        array $parameters
    ) {
        $this->repository = $repository;
        $this->connectionConfiguration = $connectionConfiguration;
        $this->method = $method;
        $this->columnName = $columnName;
        $this->parameters = $parameters;
    }

    public function getValue(): string
    {
        $result = $this->repository->send($this->connectionConfiguration, $this->method, new ParameterBag($this->parameters));

        return $result[$this->columnName] ?? '';
    }
}
