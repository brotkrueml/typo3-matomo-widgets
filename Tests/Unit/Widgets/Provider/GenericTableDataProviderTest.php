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
use Brotkrueml\MatomoWidgets\Widgets\Decorator\DecoratorInterface;
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericTableDataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Localization\LanguageService;

#[CoversClass(GenericTableDataProvider::class)]
final class GenericTableDataProviderTest extends TestCase
{
    private ConnectionConfiguration $connectionConfiguration;
    private MatomoRepository&MockObject $repositoryMock;
    private LanguageService&Stub $languageServiceStub;

    protected function setUp(): void
    {
        $this->connectionConfiguration = new ConnectionConfiguration('https://example.org/', 1, '');
        $this->repositoryMock = $this->createMock(MatomoRepository::class);

        $this->languageServiceStub = $this->createStub(LanguageService::class);
        $GLOBALS['LANG'] = $this->languageServiceStub;
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['LANG']);
    }

    #[Test]
    public function getClasses(): void
    {
        $subject = new GenericTableDataProvider(
            $this->repositoryMock,
            $this->connectionConfiguration,
            'some.method',
            [
                [
                    'column' => 'column1',
                    'classes' => 'class-1',
                ],
                [
                    'column' => 'column2',
                    'classes' => 'class-2',
                ],
                [
                    'column' => 'column3',
                ],
            ],
            [],
        );

        $expected = ['class-1', 'class-2', ''];
        $actual = $subject->getClasses();

        self::assertSame($expected, $actual);
    }

    #[Test]
    public function getColumns(): void
    {
        $subject = new GenericTableDataProvider(
            $this->repositoryMock,
            $this->connectionConfiguration,
            'some.method',
            [
                [
                    'column' => 'column1',
                ],
                [
                    'column' => 'column2',
                ],
            ],
            [],
        );

        $expected = ['column1', 'column2'];
        $actual = $subject->getColumns();

        self::assertSame($expected, $actual);
    }

    #[Test]
    public function getDecorators(): void
    {
        $decoratorStub = $this->createStub(DecoratorInterface::class);

        $subject = new GenericTableDataProvider(
            $this->repositoryMock,
            $this->connectionConfiguration,
            'some.method',
            [
                [
                    'column' => 'column1',
                ],
                [
                    'column' => 'column2',
                    'decorator' => $decoratorStub,
                ],
            ],
            [],
        );

        $expected = [null, $decoratorStub];
        $actual = $subject->getDecorators();

        self::assertSame($expected, $actual);
    }

    #[Test]
    public function getHeaders(): void
    {
        $this->languageServiceStub
            ->method('sL')
            ->with('someHeader')
            ->willReturn('some header');

        $subject = new GenericTableDataProvider(
            $this->repositoryMock,
            $this->connectionConfiguration,
            'some.method',
            [
                [
                    'column' => 'column1',
                ],
                [
                    'column' => 'column2',
                    'header' => 'someHeader',
                ],
            ],
            [],
        );

        $expected = ['', 'some header'];
        $actual = $subject->getHeaders();

        self::assertSame($expected, $actual);
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
                'column1' => 'some value',
                'column2' => 'another value',
            ],
            [
                'column1' => 'this value',
                'column2' => 'that value',
            ],
        ];

        $this->repositoryMock
            ->expects(self::once())
            ->method('send')
            ->with($this->connectionConfiguration, 'some.method', new ParameterBag($parameters))
            ->willReturn($result);

        $subject = new GenericTableDataProvider(
            $this->repositoryMock,
            $this->connectionConfiguration,
            'some.method',
            [
                [
                    'column' => 'column1',
                ],
                [
                    'column' => 'column2',
                ],
            ],
            $parameters,
        );

        $subject->getRows();
    }

    #[Test]
    public function addParameter(): void
    {
        $parameters = [
            'foo' => 'bar',
        ];

        $result = [
            [
                'column1' => 'some value',
                'column2' => 'another value',
            ],
        ];

        $this->repositoryMock
            ->expects(self::once())
            ->method('send')
            ->with($this->connectionConfiguration, 'some.method', new ParameterBag([...$parameters, ...[
                'qux' => 'quu',
            ]]))
            ->willReturn($result);

        $subject = new GenericTableDataProvider(
            $this->repositoryMock,
            $this->connectionConfiguration,
            'some.method',
            [
                [
                    'column' => 'column1',
                ],
                [
                    'column' => 'column2',
                ],
            ],
            $parameters,
        );

        $subject->addParameter('qux', 'quu');
        $subject->getRows();
    }
}
