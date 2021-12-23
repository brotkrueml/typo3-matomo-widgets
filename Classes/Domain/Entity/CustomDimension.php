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
    /**
     * @var string
     * @readonly
     */
    public $scope;

    /**
     * @var int
     * @readonly
     */
    public $idDimension;

    /**
     * @var string
     * @readonly
     */
    public $title;

    /**
     * @var string
     * @readonly
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
