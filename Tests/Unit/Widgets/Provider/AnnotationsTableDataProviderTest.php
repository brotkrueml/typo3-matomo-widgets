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
use Brotkrueml\MatomoWidgets\Parameter\PeriodResolver;
use Brotkrueml\MatomoWidgets\Widgets\Provider\AnnotationsTableDataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Localization\LanguageService;

#[CoversClass(AnnotationsTableDataProvider::class)]
final class AnnotationsTableDataProviderTest extends TestCase
{
    private readonly ConnectionConfiguration $connectionConfiguration;
    private readonly MatomoRepository&MockObject $repositoryMock;
    private readonly PeriodResolver $periodResolver;

    protected function setUp(): void
    {
        $this->connectionConfiguration = new ConnectionConfiguration('https://example.org/', 1, '');
        $this->repositoryMock = $this->createMock(MatomoRepository::class);
        $this->periodResolver = new PeriodResolver();

        $languageServiceStub = self::createStub(LanguageService::class);
        $GLOBALS['LANG'] = $languageServiceStub;
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['LANG']);
    }

    #[Test]
    public function getRows(): void
    {
        $parameters = [
            'foo' => 'bar',
            'qux' => 'quu',
        ];

        $result = [
            [
                [
                    'date' => '2020-01-01',
                    'note' => 'Annotation 2020-01-01',
                ],
                [
                    'date' => '2021-09-26',
                    'note' => 'Annotation 2021-09-26',
                ],
                [
                    'date' => '2021-05-08',
                    'note' => 'Annotation 2021-05-08',
                ],
            ],
        ];

        $this->repositoryMock
            ->expects(self::once())
            ->method('send')
            ->with($this->connectionConfiguration, 'some.method', new ParameterBag($parameters))
            ->willReturn($result);

        $subject = new AnnotationsTableDataProvider(
            $this->repositoryMock,
            $this->connectionConfiguration,
            $this->periodResolver,
            'some.method',
            [
                [
                    'column' => 'date',
                ],
                [
                    'column' => 'note',
                ],
            ],
            $parameters,
        );

        $actual = $subject->getRows();

        self::assertCount(3, $actual);
        self::assertSame([
            'date' => '2021-09-26',
            'note' => 'Annotation 2021-09-26',
        ], $actual[0]);
        self::assertSame([
            'date' => '2021-05-08',
            'note' => 'Annotation 2021-05-08',
        ], $actual[1]);
        self::assertSame([
            'date' => '2020-01-01',
            'note' => 'Annotation 2020-01-01',
        ], $actual[2]);
    }
}
