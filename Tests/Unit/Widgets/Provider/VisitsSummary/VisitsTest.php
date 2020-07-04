<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Widgets\Provider\VisitsSummary;

use Brotkrueml\MatomoWidgets\Domain\Repository\VisitsSummaryRepository;
use Brotkrueml\MatomoWidgets\Extension;
use Brotkrueml\MatomoWidgets\Widgets\Provider\VisitsSummary\Visits;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Localization\LanguageService;

class VisitsTest extends TestCase
{
    /** @var MockObject|FrontendInterface */
    private $cacheMock;

    /** @var Stub|LanguageService */
    private $languageServiceStub;

    /** @var MockObject|VisitsSummaryRepository */
    private $visitsSummaryRepositoryMock;

    protected function setUp(): void
    {
        $this->cacheMock = $this->createMock(FrontendInterface::class);

        $this->languageServiceStub = $this->createStub(LanguageService::class);
        $this->languageServiceStub
            ->method('sL')
            ->with(Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.visitsSummary.visits.dataset.label')
            ->willReturn('Visits');

        $this->visitsSummaryRepositoryMock = $this->createMock(VisitsSummaryRepository::class);
    }

    /**
     * @test
     */
    public function getChartDataRetrievesDateFromRepository(): void
    {
        $period = 'day';
        $date = 'last3';

        $expected = [
            'labels' => [
                '2020-07-01',
                '2020-07-02',
                '2020-07-03',
            ],
            'datasets' => [
                [
                    'label' => 'Visits',
                    'backgroundColor' => '#3152a0',
                    'data' => ['12', '42', '8'],
                ],
            ],
        ];

        $this->cacheMock
            ->expects(self::at(0))
            ->method('get')
            ->with('VisitsSummaryVisitsDay')
            ->willReturn(false);
        $this->cacheMock
            ->expects(self::at(1))
            ->method('set')
            ->with('VisitsSummaryVisitsDay', $expected);

        $this->visitsSummaryRepositoryMock
            ->expects(self::once())
            ->method('getVisits')
            ->with($period, $date)
            ->willReturn([
                '2020-07-01' => '12',
                '2020-07-02' => '42',
                '2020-07-03' => '8',
            ]);

        $subject = new Visits(
            $this->cacheMock,
            $this->languageServiceStub,
            $this->visitsSummaryRepositoryMock,
            $period,
            $date
        );

        self::assertSame($expected, $subject->getChartData());
    }

    /**
     * @test
     */
    public function getChartDataRetrievesDateFromCache(): void
    {
        $period = 'month';
        $date = 'last2';

        $expected = [
            'labels' => [
                '2020-07-01',
                '2020-07-02',
            ],
            'datasets' => [
                [
                    'label' => 'Visits',
                    'backgroundColor' => '#3152a0',
                    'data' => ['123', '456'],
                ],
            ],
        ];

        $this->cacheMock
            ->expects(self::once())
            ->method('get')
            ->with('VisitsSummaryVisitsMonth')
            ->willReturn($expected);

        $this->cacheMock
            ->expects(self::never())
            ->method('set');

        $subject = new Visits(
            $this->cacheMock,
            $this->languageServiceStub,
            $this->visitsSummaryRepositoryMock,
            $period,
            $date
        );

        self::assertSame($expected, $subject->getChartData());
    }
}
