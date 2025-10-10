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
final readonly class CustomDimension
{
    public function __construct(
        public string $scope,
        public int $idDimension,
        public string $title,
        public string $description,
    ) {}
}
