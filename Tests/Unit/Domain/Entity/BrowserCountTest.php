<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Domain\Entity;

use Brotkrueml\MatomoWidgets\Domain\Entity\BrowserCount;
use PHPUnit\Framework\TestCase;

final class BrowserCountTest extends TestCase
{
    /**
     * @var BrowserCount
     */
    public $subject;

    protected function setUp(): void
    {
        $this->subject = new BrowserCount('some name', 'some icon');
    }

    /**
     * @test
     */
    public function getNameReturnsNameCorrectly(): void
    {
        self::assertSame('some name', $this->subject->getName());
    }

    /**
     * @test
     */
    public function getIconReturnsNameCorrectly(): void
    {
        self::assertSame('some icon', $this->subject->getIcon());
    }

    /**
     * @test
     */
    public function getHitsReturns0AfterInitialisation(): void
    {
        self::assertSame(0, $this->subject->getHits());
    }

    /**
     * @test
     */
    public function incrementHitIncrementsCorrectly(): void
    {
        $this->subject->incrementHit();

        self::assertSame(1, $this->subject->getHits());

        $this->subject->incrementHit();

        self::assertSame(2, $this->subject->getHits());
    }
}
