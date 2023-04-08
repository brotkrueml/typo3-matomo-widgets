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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Configuration::class)]
final class ConfigurationTest extends TestCase
{
    private Configuration $subject;

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
            '',
        );
    }

    #[Test]
    public function isWidgetActiveReturnsDefinedValuesCorrectly(): void
    {
        self::assertTrue($this->subject->isWidgetActive('actionsPerDay'));
        self::assertFalse($this->subject->isWidgetActive('actionsPerMonth'));
    }

    #[Test]
    public function isWidgetActiveReturnsFalseIfWidgetIsUnknown(): void
    {
        self::assertFalse($this->subject->isWidgetActive('unknown'));
    }
}
