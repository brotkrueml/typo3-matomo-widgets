<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Widgets\Provider;

use Brotkrueml\MatomoWidgets\Extension;
use Brotkrueml\MatomoWidgets\Widgets\Provider\LinkMatomoButtonProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Localization\LanguageService;

#[CoversClass(LinkMatomoButtonProvider::class)]
final class LinkMatomoButtonProviderTest extends TestCase
{
    private LanguageService&Stub $languageServiceStub;
    private LinkMatomoButtonProvider $subject;

    protected function setUp(): void
    {
        $this->languageServiceStub = self::createStub(LanguageService::class);
        $GLOBALS['LANG'] = $this->languageServiceStub;

        $this->subject = new LinkMatomoButtonProvider('https://example.net/');
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['LANG']);
    }

    #[Test]
    public function getTitleReturnsTranslatedTitleCorrectly(): void
    {
        $this->languageServiceStub
            ->method('sL')
            ->with(Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.linkMatomo.buttonText')
            ->willReturn('Matomo link');

        self::assertSame('Matomo link', $this->subject->getTitle());
    }

    #[Test]
    public function getLinkReturnsConfiguredLinkCorrectly(): void
    {
        self::assertSame('https://example.net/', $this->subject->getLink());
    }

    #[Test]
    public function getTargetReturnsBlankTarget(): void
    {
        self::assertSame('_blank', $this->subject->getTarget());
    }
}
