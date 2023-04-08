<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Domain\Entity;

use Brotkrueml\MatomoWidgets\Domain\Entity\ScriptCount;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ScriptCount::class)]
final class ScriptCountTest extends TestCase
{
    #[Test]
    public function getScriptReturnsScriptUrlCorrectly(): void
    {
        $subject = new ScriptCount('https://example.com/some/scripts.js');

        self::assertSame('https://example.com/some/scripts.js', $subject->getScript());
    }

    #[Test]
    public function getLineReturnsDashWhenLineNotAvailable(): void
    {
        $subject = new ScriptCount('https://example.com/some/scripts.js');

        self::assertSame('-', $subject->getLine());
    }

    #[Test]
    public function getLineReturnsNumberCorrectly(): void
    {
        $subject = new ScriptCount('https://example.com/some/scripts.js:42');

        self::assertSame('42', $subject->getLine());
    }

    #[Test]
    public function getColumnReturnsDashWhenColumnNotAvailable(): void
    {
        $subject = new ScriptCount('https://example.com/some/scripts.js:42');

        self::assertSame('-', $subject->getColumn());
    }

    #[Test]
    public function getColumnReturnsNumberCorrectly(): void
    {
        $subject = new ScriptCount('https://example.com/some/scripts.js:42:123');

        self::assertSame('123', $subject->getColumn());
    }

    #[Test]
    public function getHitsReturns0AfterInitialisation(): void
    {
        $subject = new ScriptCount('https://example.com/some/scripts.js');

        self::assertSame(0, $subject->getHits());
    }

    #[Test]
    public function incrementHitsIncrementsCorrectly(): void
    {
        $subject = new ScriptCount('https://example.com/some/scripts.js');
        $subject->incrementHits();

        self::assertSame(1, $subject->getHits());

        $subject->incrementHits();

        self::assertSame(2, $subject->getHits());
    }
}
