<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Parameter;

use Brotkrueml\MatomoWidgets\Parameter\LanguageParameterResolver;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

class LanguageParameterResolverTest extends TestCase
{
    /**
     * @var Stub|BackendUserAuthentication
     */
    private $backendUserStub;

    /**
     * @var LanguageParameterResolver
     */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new LanguageParameterResolver();

        $this->backendUserStub = $this->createStub(BackendUserAuthentication::class);
        $GLOBALS['BE_USER'] = $this->backendUserStub;
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['BE_USER']);
    }

    /**
     * @test
     */
    public function resolveReturnsEmptyStringIfBackendUserIsNotDefined(): void
    {
        unset($GLOBALS['BE_USER']);

        self::assertSame('', $this->subject->resolve());
    }

    /**
     * @test
     */
    public function resolveReturnsLanguageOfBackendUserCorrectly(): void
    {
        $this->backendUserStub->uc = [
            'lang' => 'fr',
        ];

        self::assertSame('fr', $this->subject->resolve());
    }

    /**
     * @test
     */
    public function resolveReturnsEmptyStringIfLangIsNotDefined(): void
    {
        $this->backendUserStub->uc = [];

        self::assertSame('', $this->subject->resolve());
    }
}
