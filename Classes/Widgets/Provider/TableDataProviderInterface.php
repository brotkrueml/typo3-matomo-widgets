<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Widgets\Provider;

interface TableDataProviderInterface
{
    public function getClasses(): array;

    public function getColumns(): array;

    public function getDecorators(): array;

    public function getHeaders(): array;

    public function getRows(): array;
}
