<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Widgets\Provider;

use Brotkrueml\MatomoWidgets\Widgets\Decorator\DecoratorInterface;

/**
 * @internal
 */
interface TableDataProviderInterface
{
    /**
     * @return list<string>
     */
    public function getClasses(): array;

    /**
     * @return list<string>
     */
    public function getColumns(): array;

    /**
     * @return list<DecoratorInterface|null>
     */
    public function getDecorators(): array;

    /**
     * @return list<string>
     */
    public function getHeaders(): array;

    /**
     * @return list<array<string, mixed>>
     */
    public function getRows(): array;

    // @todo Require method with 4.0.0
    // public function getDatePeriod(): string;
}
