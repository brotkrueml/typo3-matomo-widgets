<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Cache;

use Brotkrueml\MatomoWidgets\Cache\CacheIdentifierCreator;
use Brotkrueml\MatomoWidgets\Connection\ConnectionConfiguration;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(CacheIdentifierCreator::class)]
final class CacheIdentifierCreatorTest extends TestCase
{
    private CacheIdentifierCreator $subject;

    protected function setUp(): void
    {
        $this->subject = new CacheIdentifierCreator();
    }

    #[Test]
    public function createEntryIdentifier(): void
    {
        $configuration = new ConnectionConfiguration('https://example.org/', 1, '');
        $method = 'some-module.some-method';
        $parameterBag = new ParameterBag([
            'somekey' => 'somevalue',
        ]);

        $actual = $this->subject->createEntryIdentifier($configuration, $method, $parameterBag);
        $expected = 'some-module_some-method_' . \md5(\serialize($configuration) . \serialize($parameterBag));

        self::assertSame($expected, $actual);
    }

    #[Test]
    public function createTag(): void
    {
        $configuration = new ConnectionConfiguration('https://example.org/', 1, '');
        $method = 'some-module.some-method';

        $actual = $this->subject->createTag($configuration, $method);
        $expected = 'some-module_some-method_' . \md5(\serialize($configuration));

        self::assertSame($expected, $actual);
    }
}
