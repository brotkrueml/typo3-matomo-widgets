<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Controller;

use Brotkrueml\MatomoWidgets\Cache\CacheIdentifierCreator;
use Brotkrueml\MatomoWidgets\Configuration\Configuration;
use Brotkrueml\MatomoWidgets\Configuration\Configurations;
use Brotkrueml\MatomoWidgets\Controller\CreateAnnotationController;
use Brotkrueml\MatomoWidgets\Domain\Repository\MatomoRepository;
use Brotkrueml\MatomoWidgets\Exception\ConnectionException;
use GuzzleHttp\Exception\RequestException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\NullLogger;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Http\ResponseFactory;
use TYPO3\CMS\Core\Localization\LanguageService;

#[CoversClass(CreateAnnotationController::class)]
#[RunTestsInSeparateProcesses]
final class CreateAnnotationControllerTest extends TestCase
{
    private FrontendInterface&MockObject $cacheMock;
    private MatomoRepository&Stub $matomoRepositoryStub;
    private CreateAnnotationController $subject;
    private BackendUserAuthentication&Stub $backendUserStub;
    private LanguageService&Stub $languageServiceStub;
    private ServerRequestInterface&Stub $serverRequestStub;

    protected function setUp(): void
    {
        $this->cacheMock = $this->createMock(FrontendInterface::class);
        $configurations = new Configurations([
            new Configuration(
                'some_identifier',
                'some title',
                'https://example.org/',
                42,
                '',
                [],
                [],
                '',
            ),
        ]);
        $this->matomoRepositoryStub = $this->createStub(MatomoRepository::class);

        $this->subject = new CreateAnnotationController(
            $this->cacheMock,
            new CacheIdentifierCreator(),
            $configurations,
            new NullLogger(),
            $this->matomoRepositoryStub,
            new ResponseFactory(),
        );

        $this->backendUserStub = $this->createStub(BackendUserAuthentication::class);
        $GLOBALS['BE_USER'] = $this->backendUserStub;

        $this->languageServiceStub = $this->createStub(LanguageService::class);
        $this->languageServiceStub
            ->method('sL')
            ->willReturnCallback(static fn(string $key): string => $key);
        $GLOBALS['LANG'] = $this->languageServiceStub;

        $this->serverRequestStub = $this->createStub(ServerRequestInterface::class);
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['BE_USER']);
        unset($GLOBALS['LANG']);
    }

    #[Test]
    #[DataProvider('dataProviderForEmptyOrMissingParameterReturnsResponseWithError')]
    public function emptyOrMissingParameterReturnsResponseWithError(array $parameters, array $expected): void
    {
        $actual = $this->invokeController($parameters);

        self::assertJsonStringEqualsJsonString(\json_encode($expected, \JSON_THROW_ON_ERROR), $actual->getBody()->getContents());
    }

    public static function dataProviderForEmptyOrMissingParameterReturnsResponseWithError(): iterable
    {
        yield 'Site identifier is empty' => [
            'parameters' => [
                'siteIdentifier' => '',
                'date' => '2021-10-07',
                'note' => 'some note',
            ],
            'expected' => [
                'status' => 'error',
                'message' => 'LLL:EXT:matomo_widgets/Resources/Private/Language/Dashboard.xlf:error.emptySiteIdentifier',
            ],
        ];

        yield 'Site identifier is missing' => [
            'parameters' => [
                'date' => '2021-10-07',
                'note' => 'some note',
            ],
            'expected' => [
                'status' => 'error',
                'message' => 'LLL:EXT:matomo_widgets/Resources/Private/Language/Dashboard.xlf:error.emptySiteIdentifier',
            ],
        ];

        yield 'Date is empty' => [
            'parameters' => [
                'siteIdentifier' => 'some_identifier',
                'date' => '',
                'note' => 'some note',
            ],
            'expected' => [
                'status' => 'error',
                'message' => 'LLL:EXT:matomo_widgets/Resources/Private/Language/Dashboard.xlf:error.emptyDate',
            ],
        ];

        yield 'Date is missing' => [
            'parameters' => [
                'siteIdentifier' => 'some_identifier',
                'note' => 'some note',
            ],
            'expected' => [
                'status' => 'error',
                'message' => 'LLL:EXT:matomo_widgets/Resources/Private/Language/Dashboard.xlf:error.emptyDate',
            ],
        ];

        yield 'Date is invalid' => [
            'parameters' => [
                'siteIdentifier' => 'some_identifier',
                'date' => 'invalid date',
                'note' => 'some note',
            ],
            'expected' => [
                'status' => 'error',
                'message' => 'LLL:EXT:matomo_widgets/Resources/Private/Language/Dashboard.xlf:error.invalidDate',
            ],
        ];

        yield 'Note is empty' => [
            'parameters' => [
                'siteIdentifier' => 'some_identifier',
                'date' => '2021-10-07',
                'note' => '',
            ],
            'expected' => [
                'status' => 'error',
                'message' => 'LLL:EXT:matomo_widgets/Resources/Private/Language/Dashboard.xlf:error.emptyNote',
            ],
        ];

        yield 'Note is missing' => [
            'parameters' => [
                'siteIdentifier' => 'some_identifier',
                'date' => '2021-10-07',
                'note' => '',
            ],
            'expected' => [
                'status' => 'error',
                'message' => 'LLL:EXT:matomo_widgets/Resources/Private/Language/Dashboard.xlf:error.emptyNote',
            ],
        ];
    }

    #[Test]
    public function useHasNoPermissionForWidgetReturnsResponseWithError(): void
    {
        $parameters = [
            'siteIdentifier' => 'some_identifier',
            'date' => '2021-10-07',
            'note' => 'some note',
        ];

        $this->stubCheckOnBackendUser(false);

        $actual = $this->invokeController($parameters);

        self::assertJsonStringEqualsJsonString('{"status":"error","message":"LLL:EXT:matomo_widgets/Resources/Private/Language/Dashboard.xlf:widgets.createAnnotation.error.noPermission"}', $actual->getBody()->getContents());
    }

    #[Test]
    public function annotationCannotBeCreatedThenReturnsResponseWithError(): void
    {
        $parameters = [
            'siteIdentifier' => 'some_identifier',
            'date' => '2021-10-07',
            'note' => 'some note',
        ];

        $this->stubCheckOnBackendUser(true);

        $this->matomoRepositoryStub
            ->method('send')
            ->willThrowException(new RequestException('error', $this->serverRequestStub));

        $actual = $this->invokeController($parameters);

        self::assertJsonStringEqualsJsonString(
            '{"status":"error","message":"An error occurred, please have a look into the TYPO3 log file for details."}',
            $actual->getBody()->getContents(),
        );
    }

    #[Test]
    public function annotationIsCreatedReturnResponseWithSuccess(): void
    {
        $parameters = [
            'siteIdentifier' => 'some_identifier',
            'date' => '2021-10-07',
            'note' => 'some note',
        ];

        $this->stubCheckOnBackendUser(true);

        $this->cacheMock
            ->expects(self::once())
            ->method('flushByTag');

        $this->matomoRepositoryStub
            ->method('send')
            ->willReturn([
                'date' => '2021-10-08',
                'note' => 'some note',
                'idNote' => 42,
            ]);

        $actual = $this->invokeController($parameters);

        self::assertJsonStringEqualsJsonString('{"status":"success"}', $actual->getBody()->getContents());
    }

    #[Test]
    public function errorCreatingAnnotationReturnsResponseWithError(): void
    {
        $parameters = [
            'siteIdentifier' => 'some_identifier',
            'date' => '2021-10-07',
            'note' => 'some note',
        ];

        $this->stubCheckOnBackendUser(true);

        $this->cacheMock
            ->expects(self::never())
            ->method('flushByTag');

        $this->matomoRepositoryStub
            ->method('send')
            ->willThrowException(new ConnectionException('some exception message'));

        $actual = $this->invokeController($parameters);

        self::assertJsonStringEqualsJsonString(
            '{"status":"error","message":"An error occurred, please have a look into the TYPO3 log file for details."}',
            $actual->getBody()->getContents(),
        );
    }

    private function stubCheckOnBackendUser(bool $hasPermission, string $identifier = 'some_identifier'): void
    {
        $this->backendUserStub
            ->method('check')
            ->with('available_widgets', 'matomo_widgets.' . $identifier . '.annotation.create')
            ->willReturn($hasPermission);
    }

    private function invokeController(array $parameters): ResponseInterface
    {
        $this->serverRequestStub
            ->method('getParsedBody')
            ->willReturn($parameters);

        $response = $this->subject->__invoke($this->serverRequestStub);
        $response->getBody()->rewind();

        return $response;
    }
}
