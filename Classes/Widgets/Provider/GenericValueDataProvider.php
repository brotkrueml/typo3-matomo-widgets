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

final class GenericValueDataProvider implements ValueDataProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

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

    public function __construct(RepositoryInterface $repository, string $method, string $columnName, array $parameters)
    {
        $this->repository = $repository;
        $this->method = $method;
        $this->columnName = $columnName;
        $this->parameters = $parameters;
    }

    public function getValue(): string
    {
        $parameterBag = new ParameterBag();
        foreach ($this->parameters as $name => $value) {
            $parameterBag->set($name, $value);
        }

        $result = $this->repository->find($this->method, $parameterBag);

        return $result[$this->columnName] ?? '';
    }
}
