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
use Brotkrueml\MatomoWidgets\Domain\Entity\JavaScriptErrorDetails;
use Brotkrueml\MatomoWidgets\Domain\Entity\ScriptCount;
use Brotkrueml\MatomoWidgets\Domain\Entity\UrlCount;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Brotkrueml\MatomoWidgets\Domain\Entity\JavaScriptErrorDetails
 */
final class JavaScriptErrorDetailsTest extends TestCase
{
    private JavaScriptErrorDetails $subject;

    public static function setUpBeforeClass(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'] = 'd-m-Y';
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'] = 'H:i';
    }

    public static function tearDownAfterClass(): void
    {
        unset($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy']);
        unset($GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm']);
    }

    protected function setUp(): void
    {
        $this->subject = new JavaScriptErrorDetails();
    }

    /**
     * @test
     */
    public function getLastAppearanceReturns0AfterInstantiation(): void
    {
        self::assertSame('01-01-1970 00:00', $this->subject->getLastAppearance());
    }

    /**
     * @test
     */
    public function compareAndStoreLastAppearanceForTheFirstTimeLastAppearanceIsSetCorrectly(): void
    {
        $this->subject->compareAndStoreLastAppearance(1643563126);

        self::assertSame('30-01-2022 17:18', $this->subject->getLastAppearance());
    }

    /**
     * @test
     */
    public function compareAndStoreLastAppearanceWithALowerTimestampLastAppearanceIsNotSetToLowerTimestamp(): void
    {
        $this->subject->compareAndStoreLastAppearance(1643563126);
        $this->subject->compareAndStoreLastAppearance(1234567890);

        self::assertSame('30-01-2022 17:18', $this->subject->getLastAppearance());
    }

    /**
     * @test
     */
    public function compareAndStoreLastAppearanceWithAHigherTimestampLastAppearanceIsOverridden(): void
    {
        $this->subject->compareAndStoreLastAppearance(1234567890);
        $this->subject->compareAndStoreLastAppearance(1643563126);

        self::assertSame('30-01-2022 17:18', $this->subject->getLastAppearance());
    }

    /**
     * @test
     */
    public function getBrowserReturnsEmptyArrayAfterInitialisation(): void
    {
        self::assertSame([], $this->subject->getBrowsers());
    }

    /**
     * @test
     */
    public function incrementBrowserCountCalledOnce(): void
    {
        $this->subject->incrementBrowserCount('Firefox', 'firefox-icon');

        self::assertCount(1, $this->subject->getBrowsers());
        $browser = $this->subject->getBrowsers()[0];
        self::assertInstanceOf(BrowserCount::class, $browser);
        self::assertSame('Firefox', $browser->getName());
        self::assertSame('firefox-icon', $browser->getIcon());
        self::assertSame(1, $browser->getHits());
    }

    /**
     * @test
     */
    public function incrementBrowserCountCalledTwiceWithSameBrowser(): void
    {
        $this->subject->incrementBrowserCount('Firefox', 'firefox-icon');
        $this->subject->incrementBrowserCount('Firefox', 'firefox-icon');

        self::assertCount(1, $this->subject->getBrowsers());
        $browser = $this->subject->getBrowsers()[0];
        self::assertInstanceOf(BrowserCount::class, $browser);
        self::assertSame('Firefox', $browser->getName());
        self::assertSame('firefox-icon', $browser->getIcon());
        self::assertSame(2, $browser->getHits());
    }

    /**
     * @test
     */
    public function incrementBrowserCountCalledTwiceWithDifferentBrowser(): void
    {
        $this->subject->incrementBrowserCount('Firefox', 'firefox-icon');
        $this->subject->incrementBrowserCount('Chrome', 'chrome-icon');

        self::assertCount(2, $this->subject->getBrowsers());
        $browser1 = $this->subject->getBrowsers()[0];
        self::assertInstanceOf(BrowserCount::class, $browser1);
        self::assertSame('Firefox', $browser1->getName());
        self::assertSame('firefox-icon', $browser1->getIcon());
        self::assertSame(1, $browser1->getHits());
        $browser2 = $this->subject->getBrowsers()[1];
        self::assertInstanceOf(BrowserCount::class, $browser2);
        self::assertSame('Chrome', $browser2->getName());
        self::assertSame('chrome-icon', $browser2->getIcon());
        self::assertSame(1, $browser2->getHits());
    }

    /**
     * @test
     */
    public function getBrowsersReturnsTheTwoDefinedBrowsersSortedCorrectly(): void
    {
        $this->subject->incrementBrowserCount('Firefox', 'firefox-icon');
        $this->subject->incrementBrowserCount('Chrome', 'chrome-icon');
        $this->subject->incrementBrowserCount('Chrome', 'chrome-icon');

        self::assertCount(2, $this->subject->getBrowsers());
        self::assertInstanceOf(BrowserCount::class, $this->subject->getBrowsers()[0]);
        self::assertInstanceOf(BrowserCount::class, $this->subject->getBrowsers()[1]);
        self::assertSame('Chrome', $this->subject->getBrowsers()[0]->getName());
        self::assertSame('Firefox', $this->subject->getBrowsers()[1]->getName());
    }

    /**
     * @test
     */
    public function getBrowsersCountReturnsCountCorrectly(): void
    {
        $this->subject->incrementBrowserCount('Firefox', 'firefox-icon');
        $this->subject->incrementBrowserCount('Chrome', 'chrome-icon');
        $this->subject->incrementBrowserCount('Chrome', 'chrome-icon');

        self::assertSame(2, $this->subject->getBrowsersCount());
    }

    /**
     * @test
     */
    public function incrementUrlCountCalledOnce(): void
    {
        $this->subject->incrementUrlCount('https://example.org/');

        self::assertCount(1, $this->subject->getUrls());
        $url = $this->subject->getUrls()[0];
        self::assertInstanceOf(UrlCount::class, $url);
        self::assertSame('https://example.org/', $url->getUrl());
        self::assertSame(1, $url->getHits());
    }

    /**
     * @test
     */
    public function incrementUrlCountCalledTwiceWithSameUrl(): void
    {
        $this->subject->incrementUrlCount('https://example.org/');
        $this->subject->incrementUrlCount('https://example.org/');

        self::assertCount(1, $this->subject->getUrls());
        $url = $this->subject->getUrls()[0];
        self::assertInstanceOf(UrlCount::class, $url);
        self::assertSame('https://example.org/', $url->getUrl());
        self::assertSame(2, $url->getHits());
    }

    /**
     * @test
     */
    public function incrementUrlCountCalledTwiceWithDifferentUrls(): void
    {
        $this->subject->incrementUrlCount('https://www.example.org/');
        $this->subject->incrementUrlCount('https://www.example.com/');

        self::assertCount(2, $this->subject->getUrls());
        $url1 = $this->subject->getUrls()[0];
        self::assertInstanceOf(UrlCount::class, $url1);
        self::assertSame('https://www.example.org/', $url1->getUrl());
        self::assertSame(1, $url1->getHits());
        $url2 = $this->subject->getUrls()[1];
        self::assertInstanceOf(UrlCount::class, $url2);
        self::assertSame('https://www.example.com/', $url2->getUrl());
        self::assertSame(1, $url2->getHits());
    }

    /**
     * @test
     */
    public function getUrlsReturnsTheTwoDefinedUrlsSortedCorrectly(): void
    {
        $this->subject->incrementUrlCount('https://www.example.org/');
        $this->subject->incrementUrlCount('https://www.example.com/');
        $this->subject->incrementUrlCount('https://www.example.com/');

        self::assertCount(2, $this->subject->getUrls());
        self::assertInstanceOf(UrlCount::class, $this->subject->getUrls()[0]);
        self::assertInstanceOf(UrlCount::class, $this->subject->getUrls()[1]);
        self::assertSame('https://www.example.com/', $this->subject->getUrls()[0]->getUrl());
        self::assertSame('https://www.example.org/', $this->subject->getUrls()[1]->getUrl());
    }

    /**
     * @test
     */
    public function getUrlsCountReturnsCountCorrectly(): void
    {
        $this->subject->incrementUrlCount('https://www.example.org/');
        $this->subject->incrementUrlCount('https://www.example.com/');
        $this->subject->incrementUrlCount('https://www.example.com/');

        self::assertSame(2, $this->subject->getUrlsCount());
    }

    /**
     * @test
     */
    public function incrementScriptCountCalledOnce(): void
    {
        $this->subject->incrementScriptCount('https://example.org/script.js');

        self::assertCount(1, $this->subject->getScripts());
        $script = $this->subject->getScripts()[0];
        self::assertInstanceOf(ScriptCount::class, $script);
        self::assertSame('https://example.org/script.js', $script->getScript());
        self::assertSame(1, $script->getHits());
    }

    /**
     * @test
     */
    public function incrementScriptCalledTwiceWithSameScript(): void
    {
        $this->subject->incrementScriptCount('https://example.org/script.js');
        $this->subject->incrementScriptCount('https://example.org/script.js');

        self::assertCount(1, $this->subject->getScripts());
        $script = $this->subject->getScripts()[0];
        self::assertInstanceOf(ScriptCount::class, $script);
        self::assertSame('https://example.org/script.js', $script->getScript());
        self::assertSame(2, $script->getHits());
    }

    /**
     * @test
     */
    public function incrementScriptCountCalledTwiceWithDifferentUrls(): void
    {
        $this->subject->incrementScriptCount('https://example.org/some.js');
        $this->subject->incrementScriptCount('https://example.org/another.js');

        self::assertCount(2, $this->subject->getScripts());
        $script1 = $this->subject->getScripts()[0];
        self::assertInstanceOf(ScriptCount::class, $script1);
        self::assertSame('https://example.org/some.js', $script1->getScript());
        self::assertSame(1, $script1->getHits());
        $script2 = $this->subject->getScripts()[1];
        self::assertInstanceOf(ScriptCount::class, $script2);
        self::assertSame('https://example.org/another.js', $script2->getScript());
        self::assertSame(1, $script2->getHits());
    }

    /**
     * @test
     */
    public function getScriptsReturnsTheTwoDefinedScriptsSortedCorrectly(): void
    {
        $this->subject->incrementScriptCount('https://example.org/some.js');
        $this->subject->incrementScriptCount('https://example.org/another.js');
        $this->subject->incrementScriptCount('https://example.org/another.js');

        self::assertCount(2, $this->subject->getScripts());
        self::assertInstanceOf(ScriptCount::class, $this->subject->getScripts()[0]);
        self::assertInstanceOf(ScriptCount::class, $this->subject->getScripts()[1]);
        self::assertSame('https://example.org/another.js', $this->subject->getScripts()[0]->getScript());
        self::assertSame('https://example.org/some.js', $this->subject->getScripts()[1]->getScript());
    }

    /**
     * @test
     */
    public function getScriptsCountReturnsCountCorrectly(): void
    {
        $this->subject->incrementScriptCount('https://example.org/some.js');
        $this->subject->incrementScriptCount('https://example.org/another.js');
        $this->subject->incrementScriptCount('https://example.org/another.js');

        self::assertSame(2, $this->subject->getScriptsCount());
    }
}
