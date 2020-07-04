<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Domain\Repository;

use Brotkrueml\MatomoWidgets\Connection\MatomoConnector;
use Brotkrueml\MatomoWidgets\Domain\Repository\VisitsSummaryRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class VisitsSummaryRepositoryTest extends TestCase
{
    /** @var MatomoConnector|MockObject */
    private $matomoConnectorMock;

    /** @var VisitsSummaryRepository */
    private $subject;

    protected function setUp(): void
    {
        $this->matomoConnectorMock = $this->createMock(MatomoConnector::class);
        $this->subject = new VisitsSummaryRepository($this->matomoConnectorMock);
    }

    /**
     * @test
     */
    public function getVisitsCallsApiCorrectly(): void
    {
        $this->matomoConnectorMock
            ->expects(self::once())
            ->method('callApi')
            ->with('VisitsSummary.getVisits', ['period' => 'day', 'date' => 'last7'])
            ->willReturn(['some' => 'result']);

        $actual = $this->subject->getVisits('day', 'last7');

        self::assertSame(['some' => 'result'], $actual);
    }
}
