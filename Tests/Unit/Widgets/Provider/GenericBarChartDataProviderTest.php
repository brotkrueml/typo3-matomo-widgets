<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Widgets\Provider;

use Brotkrueml\MatomoWidgets\Connection\ConnectionConfiguration;
use Brotkrueml\MatomoWidgets\Domain\Repository\MatomoRepository;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericBarChartDataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Localization\LanguageService;

#[CoversClass(GenericBarChartDataProvider::class)]
final class GenericBarChartDataProviderTest extends TestCase
{
    private ConnectionConfiguration $connectionConfiguration;
    private MatomoRepository&Stub $repositoryStub;
    private LanguageService&Stub $languageServiceStub;

    protected function setUp(): void
    {
        $this->connectionConfiguration = new ConnectionConfiguration('https://example.org/', 1, '');
        $this->repositoryStub = self::createStub(MatomoRepository::class);
        $this->languageServiceStub = self::createStub(LanguageService::class);

        $GLOBALS['LANG'] = $this->languageServiceStub;
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['LANG']);
    }

    #[Test]
    public function getChartData(): void
    {
        $method = 'some.method';
        $backgroundColour = '#abc';
        $barLabel = 'some bar label';
        $parameters = [
            'foo' => 'bar',
            'qux' => 'quu',
        ];

        $this->repositoryStub
            ->method('send')
            ->with($this->connectionConfiguration, $method, new ParameterBag($parameters))
            ->willReturn([
                '2020-07-16' => 1234,
                '2020-07-15' => 543,
                '2020-07-14' => 6191,
            ]);

        $this->languageServiceStub
            ->method('sL')
            ->with($barLabel)
            ->willReturn('another bar label');

        $actual = (new GenericBarChartDataProvider(
            $this->repositoryStub,
            $this->connectionConfiguration,
            $method,
            $barLabel,
            $backgroundColour,
            $parameters,
        )
        )
            ->getChartData();

        $expected = [
            'labels' => ['2020-07-16', '2020-07-15', '2020-07-14'],
            'datasets' => [
                [
                    'label' => 'another bar label',
                    'backgroundColor' => $backgroundColour,
                    'data' => [1234, 543, 6191],
                ],
            ],
        ];

        self::assertSame($expected, $actual);
    }
}
