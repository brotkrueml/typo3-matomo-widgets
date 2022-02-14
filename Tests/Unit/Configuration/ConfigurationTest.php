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
use Brotkrueml\MatomoWidgets\Domain\Entity\CustomDimension;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    /**
     * @var Configuration
     */
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
                'actionsPerDay',
            ],
            [],
            ''
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
    public function isWidgetActiveReturnsDefinedValuesCorrectly(): void
    {
        self::assertTrue($this->subject->isWidgetActive('actionsPerDay'));
        self::assertFalse($this->subject->isWidgetActive('actionsPerMonth'));
    }

    /**
     * @test
     */
    public function isWidgetActiveReturnsFalseIfWidgetIsUnknown(): void
    {
        self::assertFalse($this->subject->isWidgetActive('unknown'));
    }

    /**
     * @test
     */
    public function getCustomDimensionsIsAnEmptyArrayWhenNoCustomDimensionsAvailable(): void
    {
        self::assertSame([], $this->subject->getCustomDimensions());
    }

    /**
     * @test
     */
    public function getCustomDimensionsReturnsAnArrayOfCustomDimensionsCorrectly(): void
    {
        $customDimension1 = new CustomDimension(
            'action',
            42,
            'some title',
            'some description'
        );

        $customDimension2 = new CustomDimension(
            'visit',
            43,
            'another title',
            'another description'
        );

        $subject = new Configuration(
            'some_site_identifier',
            'some site title',
            'http://example.org/',
            42,
            'some token auth',
            [
                'actionsPerDay',
            ],
            [
                $customDimension1,
                $customDimension2,
            ],
            ''
        );

        self::assertCount(2, $subject->getCustomDimensions());
        self::assertSame($customDimension1, $subject->getCustomDimensions()[0]);
        self::assertSame($customDimension2, $subject->getCustomDimensions()[1]);
    }

    /**
     * @test
     */
    public function getPagesNotFoundTemplateReturnsTemplateCorrectly(): void
    {
        $subject = new Configuration(
            'some_site_identifier',
            'some site title',
            'http://example.org/',
            42,
            'some token auth',
            [
                'pagesNotFound',
            ],
            [],
            '404/URL = {path}/Referrer = {referrer}'
        );

        self::assertSame('404/URL = {path}/Referrer = {referrer}', $subject->getPagesNotFoundTemplate());
    }
}
