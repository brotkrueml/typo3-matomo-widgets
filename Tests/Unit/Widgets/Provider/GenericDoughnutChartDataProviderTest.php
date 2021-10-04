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
use Brotkrueml\MatomoWidgets\Domain\Repository\RepositoryInterface;
use Brotkrueml\MatomoWidgets\Extension;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericDoughnutChartDataProvider;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Localization\LanguageService;

class GenericDoughnutChartDataProviderTest extends TestCase
{
    /**
     * @var ConnectionConfiguration
     */
    private $connectionConfiguration;

    /**
     * @var Stub|RepositoryInterface
     */
    private $repositoryStub;

    /**
     * @var Stub|LanguageService
     */
    private $languageServiceStub;

    protected function setUp(): void
    {
        $this->connectionConfiguration = new ConnectionConfiguration('https://example.org/', 1, '');
        $this->repositoryStub = $this->createStub(RepositoryInterface::class);
        $this->languageServiceStub = $this->createStub(LanguageService::class);
    }

    /**
     * @test
     * @dataProvider dataProviderForGetChartData
     */
    public function getChartData(int $limit, array $rows, array $expected): void
    {
        $method = 'some.method';
        $labelColumn = 'a_label';
        $valueColumn = 'a_column';
        $backgroundColours = ['#111', '#222', '#333', '#444', '#555', '#666'];
        $parameters = [
            'foo' => 'bar',
            'qux' => 'quu',
        ];

        $this->repositoryStub
            ->method('find')
            ->with($this->connectionConfiguration, $method, new ParameterBag($parameters))
            ->willReturn($rows);

        $this->languageServiceStub
            ->method('sL')
            ->with(Extension::LANGUAGE_PATH_DASHBOARD . ':other')
            ->willReturn('Other values');

        $actual = (new GenericDoughnutChartDataProvider(
            $this->repositoryStub,
            $this->connectionConfiguration,
            $this->languageServiceStub,
            $method,
            $labelColumn,
            $valueColumn,
            $limit,
            $backgroundColours,
            $parameters
        ))->getChartData();

        self::assertSame($expected, $actual);
    }

    public function dataProviderForGetChartData(): \Generator
    {
        yield 'With no rows available, no data is returned' => [
            'limit' => 5,
            'rows' => [],
            'expected' => [
                'labels' => [],
                'datasets' => [
                    [
                        'backgroundColor' => [],
                        'data' => [],
                    ],
                ],
            ],
        ];

        $rows = [
            [
                'a_label' => 'Label A',
                'a_column' => 1357,
            ],
            [
                'a_label' => 'Label B',
                'a_column' => 2468,
            ],
            [
                'a_label' => 'Label C',
                'a_column' => 963,
            ],
            [
                'a_label' => 'Label D',
                'a_column' => 123,
            ],
            [
                'a_label' => 'Label E',
                'a_column' => 42,
            ],
        ];

        yield 'With more than (limit) rows, other values are aggregated' => [
            'limit' => 3,
            'rows' => $rows,
            'expected' => [
                'labels' => [
                    'Label A',
                    'Label B',
                    'Label C',
                    'Other values',
                ],
                'datasets' => [
                    [
                        'backgroundColor' => ['#111', '#222', '#333', '#444'],
                        'data' => [
                            1357,
                            2468,
                            963,
                            165,
                        ],
                    ],
                ],
            ],
        ];

        yield 'With more than (limit) rows, other values are not shown' => [
            'limit' => 5,
            'rows' => $rows,
            'expected' => [
                'labels' => [
                    'Label A',
                    'Label B',
                    'Label C',
                    'Label D',
                    'Label E',
                ],
                'datasets' => [
                    [
                        'backgroundColor' => ['#111', '#222', '#333', '#444', '#555'],
                        'data' => [
                            1357,
                            2468,
                            963,
                            123,
                            42,
                        ],
                    ],
                ],
            ],
        ];

        yield 'With equal (limit) rows, other values are not shown' => [
            'limit' => 5,
            'rows' => $rows,
            'expected' => [
                'labels' => [
                    'Label A',
                    'Label B',
                    'Label C',
                    'Label D',
                    'Label E',
                ],
                'datasets' => [
                    [
                        'backgroundColor' => ['#111', '#222', '#333', '#444', '#555'],
                        'data' => [
                            1357,
                            2468,
                            963,
                            123,
                            42,
                        ],
                    ],
                ],
            ],
        ];
    }
}
