<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Domain\Repository;

use Brotkrueml\MatomoWidgets\Connection\ConnectionConfiguration;
use Brotkrueml\MatomoWidgets\Connection\MatomoConnector;
use Brotkrueml\MatomoWidgets\Domain\Repository\MatomoRepository;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MatomoRepositoryTest extends TestCase
{
    /**
     * @var MatomoConnector|MockObject
     */
    private $connectorMock;
    private ConnectionConfiguration $connectionConfiguration;
    private MatomoRepository $subject;

    protected function setUp(): void
    {
        $this->connectorMock = $this->createMock(MatomoConnector::class);
        $this->connectionConfiguration = new ConnectionConfiguration('https://example.net', 3, '');
        $this->subject = new MatomoRepository($this->connectorMock);
    }

    /**
     * @test
     */
    public function findCallsConnectorAndReturnsResult(): void
    {
        $parameterBag = new ParameterBag();
        $expected = [
            'foo' => 'qux',
        ];

        $this->connectorMock
            ->expects(self::once())
            ->method('callApi')
            ->with($this->connectionConfiguration, 'foo.bar', $parameterBag)
            ->willReturn($expected);

        self::assertSame($expected, $this->subject->send($this->connectionConfiguration, 'foo.bar', $parameterBag));
    }
}
