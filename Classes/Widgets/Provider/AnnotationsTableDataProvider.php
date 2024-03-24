<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Widgets\Provider;

use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;

/**
 * @internal
 */
final class AnnotationsTableDataProvider extends GenericTableDataProvider
{
    public function getRows(): array
    {
        $rows = $this->repository->send($this->connectionConfiguration, $this->method, new ParameterBag($this->parameters))[0] ?? [];
        \usort($rows, static fn(array $a, array $b): int => -($a['date'] <=> $b['date']));

        return $rows;
    }

    public function getDatePeriod(): string
    {
        // Display no period for annotations
        return '';
    }
}
