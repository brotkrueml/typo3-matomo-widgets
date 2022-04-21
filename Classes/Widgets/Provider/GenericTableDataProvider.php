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
use Brotkrueml\MatomoWidgets\Parameter\LanguageParameterResolver;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use Brotkrueml\MatomoWidgets\Widgets\Decorator\DecoratorInterface;
use TYPO3\CMS\Core\Localization\LanguageService;

/**
 * @internal
 * @phpstan-type Column array{column: string, header?: string, decorator?: DecoratorInterface, classes?: string}
 */
class GenericTableDataProvider implements TableDataProviderInterface
{
    protected MatomoRepositoryInterface $repository;
    protected ConnectionConfiguration $connectionConfiguration;
    protected string $method;
    /**
     * @var array<string, string|LanguageParameterResolver>
     */
    protected array $parameters;

    private LanguageService $languageService;
    /**
     * @var list<Column>
     */
    private array $columns;

    /**
     * @param list<Column> $columns
     * @param array<string, string|LanguageParameterResolver> $parameters
     */
    public function __construct(
        MatomoRepositoryInterface $repository,
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
     * @param string|LanguageParameterResolver $value
     */
    public function addParameter(string $name, $value): void
    {
        $this->parameters[$name] ??= $value;
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
        $columns = [];
        foreach ($this->columns as $column) {
            $columns[] = $column['column'];
        }

        return $columns;
    }

    public function getDecorators(): array
    {
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
            $headers[] = isset($column['header']) ? $this->languageService->sL($column['header']) : '';
        }

        return $headers;
    }

    public function getRows(): array
    {
        return $this->repository->send($this->connectionConfiguration, $this->method, new ParameterBag($this->parameters));
    }
}
