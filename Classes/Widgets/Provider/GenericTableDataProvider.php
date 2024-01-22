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
use Brotkrueml\MatomoWidgets\DateTime\DateRange;
use Brotkrueml\MatomoWidgets\DateTime\MatomoPeriodResolver;
use Brotkrueml\MatomoWidgets\Domain\Repository\MatomoRepository;
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
    /**
     * @param list<Column> $columns
     * @param array<string, string|LanguageParameterResolver> $parameters
     */
    public function __construct(
        protected readonly MatomoPeriodResolver $periodResolver,
        protected readonly MatomoRepository $repository,
        protected readonly ConnectionConfiguration $connectionConfiguration,
        protected string $method,
        private readonly array $columns,
        protected array $parameters,
    ) {}

    public function addParameter(string $name, string|LanguageParameterResolver $value): void
    {
        $this->parameters[$name] ??= $value;
    }

    public function getDateRange(): ?DateRange
    {
        if (! \is_string($this->parameters['period'] ?? false)) {
            return null;
        }
        if (! \is_string($this->parameters['date'] ?? false)) {
            return null;
        }

        return $this->periodResolver->toDateRange($this->parameters['period'], $this->parameters['date']);
    }

    /**
     * @return list<string>
     */
    public function getClasses(): array
    {
        $classes = [];
        foreach ($this->columns as $column) {
            $classes[] = (string)($column['classes'] ?? '');
        }

        return $classes;
    }

    /**
     * @return list<string>
     */
    public function getColumns(): array
    {
        $columns = [];
        foreach ($this->columns as $column) {
            $columns[] = $column['column'];
        }

        return $columns;
    }

    /**
     * @return list<DecoratorInterface|null>
     */
    public function getDecorators(): array
    {
        $decorators = [];
        foreach ($this->columns as $column) {
            $decorators[] = $column['decorator'] ?? null;
        }

        return $decorators;
    }

    /**
     * @return list<string>
     */
    public function getHeaders(): array
    {
        $headers = [];
        foreach ($this->columns as $column) {
            $headers[] = isset($column['header']) ? $this->getLanguageService()->sL($column['header']) : '';
        }

        return $headers;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getRows(): array
    {
        return $this->repository->send($this->connectionConfiguration, $this->method, new ParameterBag($this->parameters));
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
