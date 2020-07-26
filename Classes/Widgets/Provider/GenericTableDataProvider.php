<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Widgets\Provider;

use Brotkrueml\MatomoWidgets\Domain\Repository\RepositoryInterface;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use TYPO3\CMS\Core\Localization\LanguageService;

class GenericTableDataProvider implements TableDataProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var LanguageService
     */
    private $languageService;

    /**
     * @var string
     */
    private $method;

    /**
     * @var array<string,string>
     */
    private $columns;

    /**
     * @var array
     */
    private $parameters;

    public function __construct(
        RepositoryInterface $repository,
        LanguageService $languageService,
        string $method,
        array $columns,
        array $parameters
    ) {
        $this->repository = $repository;
        $this->languageService = $languageService;
        $this->method = $method;
        $this->columns = $columns;
        $this->parameters = $parameters;
    }

    public function addParameter(string $name, $value)
    {
        $this->parameters[$name] = $this->parameters[$name] ?? $value;
    }

    public function getClasses(): array
    {
        $classes = $this->columns;
        \array_walk($classes, static function (&$class): void {
            $class = $class['classes'] ?? '';
        });

        return $classes;
    }

    public function getColumns(): array
    {
        $columns = $this->columns;
        \array_walk($columns, static function (&$column): void {
            $column = $column['column'] ?? 'unknown';
        });

        return $columns;
    }

    public function getDecorators(): array
    {
        $decorators = $this->columns;
        \array_walk($decorators, static function (&$decorator): void {
            $decorator = $decorator['decorator'] ?? null;
        });

        return $decorators;
    }

    public function getHeaders(): array
    {
        $headers = $this->columns;
        \array_walk($headers, function (&$header): void {
            $header = isset($header['header']) ? $this->languageService->sL($header['header']) : '';
        });

        return $headers;
    }

    public function getRows(): array
    {
        return $this->repository->find($this->method, new ParameterBag($this->parameters));
    }
}
