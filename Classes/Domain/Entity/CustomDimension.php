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
 * @psalm-immutable
 * @internal
 */
final class CustomDimension
{
    /**
     * @var string
     */
    public $scope;

    /**
     * @var int
     */
    public $idDimension;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $description;

    public function __construct(string $scope, int $idDimension, string $title, string $description)
    {
        $this->scope = $scope;
        $this->idDimension = $idDimension;
        $this->title = $title;
        $this->description = $description;
    }
}
