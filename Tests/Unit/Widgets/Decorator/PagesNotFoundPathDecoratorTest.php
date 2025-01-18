<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Widgets\Decorator;

use Brotkrueml\MatomoWidgets\Extension;
use Brotkrueml\MatomoWidgets\Widgets\Decorator\PagesNotFoundPathDecorator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Localization\LanguageService;

#[CoversClass(PagesNotFoundPathDecorator::class)]
final class PagesNotFoundPathDecoratorTest extends TestCase
{
    private const DEFAULT_TEMPLATE = 'Error 404 | Path = {path} | Referrer = {referrer}';

    protected function setUp(): void
    {
        $languageServiceStub = self::createStub(LanguageService::class);
        $languageServiceStub
            ->method('sL')
            ->with(Extension::LANGUAGE_PATH_DASHBOARD . ':referrer')
            ->willReturn('Referrer');

        $GLOBALS['LANG'] = $languageServiceStub;
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['LANG']);
    }

    #[Test]
    #[DataProvider('dataProviderForDecorate')]
    public function decorate(string $template, string $value, string $expected): void
    {
        $subject = new PagesNotFoundPathDecorator($template);

        self::assertSame($expected, $subject->decorate($value));
    }

    public static function dataProviderForDecorate(): iterable
    {
        yield 'Empty value returns empty value' => [
            'template' => self::DEFAULT_TEMPLATE,
            'value' => '',
            'expected' => '',
        ];

        yield 'Value does not obey template structure returns value untouched' => [
            'template' => self::DEFAULT_TEMPLATE,
            'value' => 'Error 404 | some path | some referrer',
            'expected' => 'Error 404 | some path | some referrer',
        ];

        yield 'Only a path is given, no referrer (path placeholder is first in template)' => [
            'template' => self::DEFAULT_TEMPLATE,
            'value' => 'Error 404 | Path = /some/path | Referrer = ',
            'expected' => '/some/path',
        ];

        yield 'Path and referrer are given (path placeholder is first in template)' => [
            'template' => self::DEFAULT_TEMPLATE,
            'value' => 'Error 404 | Path = /some/path | Referrer = https://example.org/',
            'expected' => '/some/path<br><b>Referrer:</b> https://example.org/',
        ];

        yield 'Path with query parameters' => [
            'template' => self::DEFAULT_TEMPLATE,
            'value' => 'Error 404 | Path = /some/path?a=b&x=y | Referrer = ',
            'expected' => '/some/path?a=b&x=y',
        ];

        yield 'Value is trimmed' => [
            'template' => self::DEFAULT_TEMPLATE,
            'value' => ' Error 404 | Path = /some/path | Referrer = ',
            'expected' => '/some/path',
        ];

        yield 'Only a path is given, no referrer (referrer placeholder is first in template)' => [
            'template' => 'Error 404 | Referrer = {referrer} | Path = {path}',
            'value' => 'Error 404 | Referrer =  | Path = /some/path',
            'expected' => '/some/path',
        ];

        yield 'Path and referrer are given (referrer placeholder is first in template)' => [
            'template' => 'Error 404 | Referrer = {referrer} | Path = {path}',
            'value' => 'Error 404 | Referrer = https://example.org/ | Path = /some/path',
            'expected' => '/some/path<br><b>Referrer:</b> https://example.org/',
        ];

        yield 'Only path is available in template' => [
            'template' => '404 / Path = {path}',
            'value' => '404 / Path = /some/path',
            'expected' => '/some/path',
        ];

        yield 'Template with statusCode placeholder' => [
            'template' => 'Error {statusCode} | Path = {path} | From = {referrer}',
            'value' => 'Error 404 | Path = /some/path | From = ',
            'expected' => '/some/path',
        ];
    }
}
