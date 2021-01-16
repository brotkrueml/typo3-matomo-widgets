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
use Brotkrueml\MatomoWidgets\Domain\Repository\RepositoryInterface;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;

final class GenericValueDataProvider implements ValueDataProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var ConnectionConfiguration
     */
    private $connectionConfiguration;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $columnName;

    /**
     * @var array
     */
    private $parameters;

    public function __construct(
        RepositoryInterface $repository,
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
        $result = $this->repository->find($this->connectionConfiguration, $this->method, new ParameterBag($this->parameters));

        return $result[$this->columnName] ?? '';
    }
}
