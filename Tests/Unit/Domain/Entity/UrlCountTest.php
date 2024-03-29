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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(UrlCount::class)]
final class UrlCountTest extends TestCase
{
    private UrlCount $subject;

    protected function setUp(): void
    {
        $this->subject = new UrlCount('https://example.com/some/page/');
    }

    #[Test]
    public function getUrlReturnsScriptUrlCorrectly(): void
    {
        self::assertSame('https://example.com/some/page/', $this->subject->getUrl());
    }

    #[Test]
    public function getHitsReturns0AfterInitialisation(): void
    {
        self::assertSame(0, $this->subject->getHits());
    }

    #[Test]
    public function incrementHitsIncrementsCorrectly(): void
    {
        $this->subject->incrementHits();

        self::assertSame(1, $this->subject->getHits());

        $this->subject->incrementHits();

        self::assertSame(2, $this->subject->getHits());
    }
}
