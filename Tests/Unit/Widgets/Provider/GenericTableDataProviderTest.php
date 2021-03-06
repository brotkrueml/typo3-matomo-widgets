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
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use Brotkrueml\MatomoWidgets\Widgets\Decorator\DecoratorInterface;
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericTableDataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Localization\LanguageService;

class GenericTableDataProviderTest extends TestCase
{
    /**
     * @var ConnectionConfiguration
     */
    private $connectionConfiguration;

    /**
     * @var MockObject|RepositoryInterface
     */
    private $repositoryMock;

    /**
     * @var Stub|LanguageService
     */
    private $languageServiceStub;

    protected function setUp(): void
    {
        $this->connectionConfiguration = new ConnectionConfiguration('https://example.org/', 1, '');
        $this->repositoryMock = $this->createMock(RepositoryInterface::class);
        $this->languageServiceStub = $this->createStub(LanguageService::class);
    }

    /**
     * @test
     */
    public function getClasses(): void
    {
        $subject = new GenericTableDataProvider(
            $this->repositoryMock,
            $this->connectionConfiguration,
            $this->languageServiceStub,
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
            []
        );

        $expected = ['class-1', 'class-2', ''];
        $actual = $subject->getClasses();

        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getColumns(): void
    {
        $subject = new GenericTableDataProvider(
            $this->repositoryMock,
            $this->connectionConfiguration,
            $this->languageServiceStub,
            'some.method',
            [
                [
                    'column' => 'column1',
                ],
                [
                    'column' => 'column2',
                ],
                [
                ],
            ],
            []
        );

        $expected = ['column1', 'column2', 'unknown'];
        $actual = $subject->getColumns();

        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getDecorators(): void
    {
        $decoratorStub = $this->createStub(DecoratorInterface::class);

        $subject = new GenericTableDataProvider(
            $this->repositoryMock,
            $this->connectionConfiguration,
            $this->languageServiceStub,
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
            []
        );

        $expected = [null, $decoratorStub];
        $actual = $subject->getDecorators();

        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getHeaders(): void
    {
        $this->languageServiceStub
            ->method('sL')
            ->with('someHeader')
            ->willReturn('some header');

        $subject = new GenericTableDataProvider(
            $this->repositoryMock,
            $this->connectionConfiguration,
            $this->languageServiceStub,
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
            []
        );

        $expected = ['', 'some header'];
        $actual = $subject->getHeaders();

        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
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
            ->method('find')
            ->with($this->connectionConfiguration, 'some.method', new ParameterBag($parameters))
            ->willReturn($result);

        $subject = new GenericTableDataProvider(
            $this->repositoryMock,
            $this->connectionConfiguration,
            $this->languageServiceStub,
            'some.method',
            [
                [
                    'column' => 'column1',
                ],
                [
                    'column' => 'column2',
                ],
            ],
            $parameters
        );

        $subject->getRows();
    }

    /**
     * @test
     */
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
            ->method('find')
            ->with($this->connectionConfiguration, 'some.method', new ParameterBag(\array_merge($parameters, ['qux' => 'quu'])))
            ->willReturn($result);

        $subject = new GenericTableDataProvider(
            $this->repositoryMock,
            $this->connectionConfiguration,
            $this->languageServiceStub,
            'some.method',
            [
                [
                    'column' => 'column1',
                ],
                [
                    'column' => 'column2',
                ],
            ],
            $parameters
        );

        $subject->addParameter('qux', 'quu');
        $subject->getRows();
    }
}
