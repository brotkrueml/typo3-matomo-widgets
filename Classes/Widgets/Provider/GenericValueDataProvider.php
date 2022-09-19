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
    /**
     * @param array<string, string> $parameters
     */
    public function __construct(
        private readonly MatomoRepositoryInterface $repository,
        private readonly ConnectionConfiguration $connectionConfiguration,
        private readonly string $method,
        private readonly string $columnName,
        private readonly array $parameters
    ) {
    }

    public function getValue(): string
    {
        $result = $this->repository->send($this->connectionConfiguration, $this->method, new ParameterBag($this->parameters));

        return $result[$this->columnName] ?? '';
    }
}
