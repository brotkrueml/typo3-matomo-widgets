<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Domain\Entity;

use Brotkrueml\MatomoWidgets\Domain\Entity\CustomDimension;
use PHPUnit\Framework\TestCase;

class CustomDimensionTest extends TestCase
{
    /**
     * @test
     */
    public function propertiesAreSetInTheCorrectOrder(): void
    {
        $subject = new CustomDimension('action', 42, 'Some title', 'Some description');

        self::assertSame('action', $subject->scope);
        self::assertSame(42, $subject->idDimension);
        self::assertSame('Some title', $subject->title);
        self::assertSame('Some description', $subject->description);
    }
}
