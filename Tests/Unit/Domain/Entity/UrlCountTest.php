<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Domain\Entity;

use Brotkrueml\MatomoWidgets\Domain\Entity\UrlCount;
use PHPUnit\Framework\TestCase;

final class UrlCountTest extends TestCase
{
    /**
     * @var UrlCount
     */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new UrlCount('https://example.com/some/page/');
    }

    /**
     * @test
     */
    public function getUrlReturnsScriptUrlCorrectly(): void
    {
        self::assertSame('https://example.com/some/page/', $this->subject->getUrl());
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
    public function incrementHitsIncrementsCorrectly(): void
    {
        $this->subject->incrementHits();

        self::assertSame(1, $this->subject->getHits());

        $this->subject->incrementHits();

        self::assertSame(2, $this->subject->getHits());
    }
}
