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
use Brotkrueml\MatomoWidgets\Widgets\Decorator\DecoratorInterface;
use TYPO3\CMS\Core\Localization\LanguageService;

class GenericTableDataProvider implements TableDataProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var ConnectionConfiguration
     */
    protected $connectionConfiguration;

    /**
     * @var LanguageService
     */
    private $languageService;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    private $columns;

    /**
     * @var array
     */
    protected $parameters;

    public function __construct(
        RepositoryInterface $repository,
        ConnectionConfiguration $connectionConfiguration,
        LanguageService $languageService,
        string $method,
        array $columns,
        array $parameters
    ) {
        $this->repository = $repository;
        $this->connectionConfiguration = $connectionConfiguration;
        $this->languageService = $languageService;
        $this->method = $method;
        $this->columns = $columns;
        $this->parameters = $parameters;
    }

    /**
     * @param string|DecoratorInterface $value
     */
    public function addParameter(string $name, $value): void
    {
        $this->parameters[$name] = $this->parameters[$name] ?? $value;
    }

    public function getClasses(): array
    {
        /** @var string[] $classes */
        $classes = [];
        foreach ($this->columns as $column) {
            $classes[] = (string)($column['classes'] ?? '');
        }

        return $classes;
    }

    public function getColumns(): array
    {
        /** @var string[] $classes */
        $columns = [];
        foreach ($this->columns as $column) {
            $columns[] = (string)($column['column'] ?? 'unknown');
        }

        return $columns;
    }

    public function getDecorators(): array
    {
        /** @var ?DecoratorInterface[] $classes */
        $decorators = [];
        foreach ($this->columns as $column) {
            $decorators[] = $column['decorator'] ?? null;
        }

        return $decorators;
    }

    public function getHeaders(): array
    {
        /** @var string[] $headers */
        $headers = [];
        foreach ($this->columns as $column) {
            $headers[] = isset($column['header']) ? $this->languageService->sL((string)$column['header']) : '';
        }

        return $headers;
    }

    public function getRows(): array
    {
        return $this->repository->find($this->connectionConfiguration, $this->method, new ParameterBag($this->parameters));
    }
}
