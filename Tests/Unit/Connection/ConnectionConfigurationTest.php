<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Connection;

use Brotkrueml\MatomoWidgets\Connection\ConnectionConfiguration;
use PHPUnit\Framework\TestCase;

class ConnectionConfigurationTest extends TestCase
{
    /**
     * @test
     */
    public function getUrlReturnsGivenUrl(): void
    {
        $subject = new ConnectionConfiguration('https://example.org/', 1, '');

        self::assertSame('https://example.org/', $subject->getUrl());
    }

    /**
     * @test
     */
    public function getIdSiteReturnsGivenIdSite(): void
    {
        $subject = new ConnectionConfiguration('https://example.org/', 1, '');

        self::assertSame(1, $subject->getIdSite());
    }

    /**
     * @test
     */
    public function getTokenAuthReturnsGivenTokenAuthIfNotEmpty(): void
    {
        $subject = new ConnectionConfiguration('https://example.org/', 1, 'secrettoken');

        self::assertSame('secrettoken', $subject->getTokenAuth());
    }

    /**
     * @test
     */
    public function getTokenAuthReturnsAnonymousIfGivenTokenAuthIsEmpty(): void
    {
        $subject = new ConnectionConfiguration('https://example.org/', 1, '');

        self::assertSame('anonymous', $subject->getTokenAuth());
    }
}
