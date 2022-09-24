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

final class ConnectionConfigurationTest extends TestCase
{
    /**
     * @test
     */
    public function tokenAuthHasAnonymousValueIfGivenTokenAuthIsEmpty(): void
    {
        $subject = new ConnectionConfiguration('https://example.org/', 1, '');

        self::assertSame('anonymous', $subject->tokenAuth);
    }
}
