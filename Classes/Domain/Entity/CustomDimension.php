<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Domain\Entity;

/**
 * @internal
 */
final class CustomDimension
{
    public function __construct(
        public readonly string $scope,
        public readonly int $idDimension,
        public readonly string $title,
        public readonly string $description
    ) {
    }
}
