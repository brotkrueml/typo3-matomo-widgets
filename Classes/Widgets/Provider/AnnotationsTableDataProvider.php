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

class AnnotationsTableDataProvider extends GenericTableDataProvider
{
    public function getRows(): array
    {
        $rows = $this->repository->find($this->connectionConfiguration, $this->method, new ParameterBag($this->parameters))[0] ?? [];
        \usort($rows, static function (array $a, array $b): int {
            return -($a['date'] <=> $b['date']);
        });

        return $rows;
    }
}
