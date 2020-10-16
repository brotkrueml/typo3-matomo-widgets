<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Configuration;

use Brotkrueml\MatomoWidgets\Configuration\Configuration;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    /** @var Configuration */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new Configuration(
            'some_site_identifier',
            'some site title',
            'http://example.org/',
            42,
            'some token auth',
            [
                'matomoWidgetsEnableActionsPerDay' => true,
                'matomoWidgetsEnableActionsPerMonth' => false,
            ]
        );
    }

    /**
     * @test
     */
    public function gettersReturnCorrectContent(): void
    {
        self::assertSame('some_site_identifier', $this->subject->getSiteIdentifier());
        self::assertSame('some site title', $this->subject->getSiteTitle());
        self::assertSame('http://example.org/', $this->subject->getUrl());
        self::assertSame(42, $this->subject->getIdSite());
        self::assertSame('some token auth', $this->subject->getTokenAuth());
    }

    /**
     * @test
     */
    public function isWidgetEnabledReturnsDefinedValuesCorrectly(): void
    {
        self::assertTrue($this->subject->isWidgetEnabled('matomoWidgetsEnableActionsPerDay'));
        self::assertFalse($this->subject->isWidgetEnabled('matomoWidgetsEnableActionsPerMonth'));
    }

    /**
     * @test
     */
    public function isWidgetEnabledReturnsFalseIfWidgetIsUnknown(): void
    {
        self::assertFalse($this->subject->isWidgetEnabled('unknown'));
    }
}
