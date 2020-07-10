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

    public function getTableClasses(): array
    {
        $aligns = $this->columns;
        \array_walk($aligns, static function (&$align): void {
            $align = $align['align'] ?? '';
        });

        return $aligns;
    }

    public function getTableColumns(): array
    {
        $columns = $this->columns;
        \array_walk($columns, static function (&$column): void {
            $column = $column['column'] ?? 'unknown';
        });

        return $columns;
    }

    public function getTableHeaders(): array
    {
        $headers = $this->columns;
        \array_walk($headers, function (&$header): void {
            $header = $this->languageService->sL($header['header'] ?? 'unknown');
        });

        return $headers;
    }

    public function getTableRows(): array
    {
        return $this->getMatomoData();
    }

    private function getMatomoData(): array
    {
        $parameterBag = new ParameterBag();
        foreach ($this->parameters as $name => $value) {
            $parameterBag->set($name, (string)$value);
        }

        return $this->repository->find($this->method, $parameterBag);
    }
}
