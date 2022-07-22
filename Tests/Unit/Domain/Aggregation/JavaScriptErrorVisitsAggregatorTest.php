<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Domain\Aggregation;

use Brotkrueml\MatomoWidgets\Domain\Aggregation\JavaScriptErrorDetailsAggregator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Brotkrueml\MatomoWidgets\Domain\Aggregation\JavaScriptErrorDetailsAggregator
 */
final class JavaScriptErrorVisitsAggregatorTest extends TestCase
{
    private JavaScriptErrorDetailsAggregator $subject;

    protected function setUp(): void
    {
        $this->subject = new JavaScriptErrorDetailsAggregator();
    }

    /**
     * @test
     * @dataProvider dataProviderForAggregate
     */
    public function aggregate(array $details, int $expectedBrowserCount, int $expectedScriptCount, int $expectedUrlsCount): void
    {
        $actual = $this->subject->aggregate($details);

        self::assertSame($expectedBrowserCount, $actual->getBrowsersCount());
        self::assertSame($expectedScriptCount, $actual->getScriptsCount());
        self::assertSame($expectedUrlsCount, $actual->getUrlsCount());
    }

    public function dataProviderForAggregate(): iterable
    {
        yield 'No details available' => [
            'details' => [],
            'expectedBrowserCount' => 0,
            'expectedScriptCount' => 0,
            'expectedUrlsCount' => 0,
        ];

        yield 'Action details with no event are filtered out' => [
            'details' => [
                [
                    'browserName' => 'Firefox',
                    'browserIcon' => 'firefox-icon',
                    'browserVersion' => '42.0',
                    'actionDetails' => [
                        [
                            'type' => 'action',
                        ],
                    ],
                ],
            ],
            'expectedBrowserCount' => 0,
            'expectedScriptCount' => 0,
            'expectedUrlsCount' => 0,
        ];

        yield 'Action details with event but wrong event category are filtered out' => [
            'details' => [
                [
                    'browserName' => 'Firefox',
                    'browserIcon' => 'firefox-icon',
                    'browserVersion' => '42.0',
                    'actionDetails' => [
                        [
                            'type' => 'event',
                            'eventCategory' => 'some-event',
                        ],
                    ],
                ],
            ],
            'expectedBrowserCount' => 0,
            'expectedScriptCount' => 0,
            'expectedUrlsCount' => 0,
        ];

        yield 'Action details with one correct event category in one session is considered correctly' => [
            'details' => [
                [
                    'browserName' => 'Firefox',
                    'browserIcon' => 'firefox-icon',
                    'browserVersion' => '42.0',
                    'actionDetails' => [
                        [
                            'type' => 'action',
                        ],
                        [
                            'type' => 'event',
                            'eventAction' => 'https://example.org/script.js',
                            'eventCategory' => 'JavaScript Errors',
                            'timestamp' => 1643620160,
                            'url' => 'https://example.org/',
                        ],
                        [
                            'type' => 'event',
                            'eventCategory' => 'some-event',
                        ],
                    ],
                ],
            ],
            'expectedBrowserCount' => 1,
            'expectedScriptCount' => 1,
            'expectedUrlsCount' => 1,
        ];

        yield 'Action details with two correct event categories in one session is considered correctly' => [
            'details' => [
                [
                    'browserName' => 'Firefox',
                    'browserIcon' => 'firefox-icon',
                    'browserVersion' => '42.0',
                    'actionDetails' => [
                        [
                            'type' => 'action',
                        ],
                        [
                            'type' => 'event',
                            'eventAction' => 'https://example.org/script.js:42:11',
                            'eventCategory' => 'JavaScript Errors',
                            'timestamp' => 1643620160,
                            'url' => 'https://example.org/',
                        ],
                        [
                            'type' => 'event',
                            'eventCategory' => 'some-event',
                        ],
                        [
                            'type' => 'event',
                            'eventAction' => 'https://example.org/script.js:43:12',
                            'eventCategory' => 'JavaScript Errors',
                            'timestamp' => 1643620162,
                            'url' => 'https://example.org/',
                        ],
                    ],
                ],
            ],
            'expectedBrowserCount' => 1,
            'expectedScriptCount' => 2,
            'expectedUrlsCount' => 1,
        ];

        yield 'Action details with two correct event categories in two session is considered correctly' => [
            'details' => [
                [
                    'browserName' => 'Firefox',
                    'browserIcon' => 'firefox-icon',
                    'browserVersion' => '42.0',
                    'actionDetails' => [
                        [
                            'type' => 'event',
                            'eventAction' => 'https://example.org/script.js:42:11',
                            'eventCategory' => 'JavaScript Errors',
                            'timestamp' => 1643620160,
                            'url' => 'https://example.org/',
                        ],
                        [
                            'type' => 'event',
                            'eventAction' => 'https://example.org/script.js:43:12',
                            'eventCategory' => 'JavaScript Errors',
                            'timestamp' => 1643620162,
                            'url' => 'https://example.org/',
                        ],
                    ],
                ],
                [
                    'browserName' => 'Chrome',
                    'browserIcon' => 'chrome-icon',
                    'browserVersion' => '42.0',
                    'actionDetails' => [
                        [
                            'type' => 'event',
                            'eventAction' => 'https://example.org/script.js:44:13',
                            'eventCategory' => 'JavaScript Errors',
                            'timestamp' => 1643620164,
                            'url' => 'https://example.org/some-other-page/',
                        ],
                        [
                            'type' => 'event',
                            'eventAction' => 'https://example.org/script.js:43:12',
                            'eventCategory' => 'JavaScript Errors',
                            'timestamp' => 1643620166,
                            'url' => 'https://example.org/',
                        ],
                    ],
                ],
            ],
            'expectedBrowserCount' => 2,
            'expectedScriptCount' => 3,
            'expectedUrlsCount' => 2,
        ];
    }
}
