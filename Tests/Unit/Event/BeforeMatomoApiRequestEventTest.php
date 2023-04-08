<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Event;

use Brotkrueml\MatomoWidgets\Event\BeforeMatomoApiRequestEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(BeforeMatomoApiRequestEvent::class)]
final class BeforeMatomoApiRequestEventTest extends TestCase
{
    private BeforeMatomoApiRequestEvent $subject;

    protected function setUp(): void
    {
        $this->subject = new BeforeMatomoApiRequestEvent(42, 'some-token');
    }

    #[Test]
    public function getIdSite(): void
    {
        self::assertSame(42, $this->subject->getIdSite());
    }

    #[Test]
    public function getTokenAuth(): void
    {
        self::assertSame('some-token', $this->subject->getTokenAuth());
    }

    #[Test]
    public function setIdSite(): void
    {
        $this->subject->setIdSite(100);

        self::assertSame(100, $this->subject->getIdSite());
    }

    #[Test]
    public function setTokenAuth(): void
    {
        $this->subject->setTokenAuth('another-token');

        self::assertSame('another-token', $this->subject->getTokenAuth());
    }
}
