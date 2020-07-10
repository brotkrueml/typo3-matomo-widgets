<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Widgets\Provider;

use Brotkrueml\MatomoWidgets\Extension;
use Brotkrueml\MatomoWidgets\Widgets\Provider\LinkMatomoProvider;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Localization\LanguageService;

class LinkMatomoProviderTest extends TestCase
{
    /** @var Stub|ExtensionConfiguration */
    private $extensionConfigurationStub;

    /** @var Stub|LanguageService */
    private $languageServiceStub;

    /** @var LinkMatomoProvider */
    private $subject;

    protected function setUp(): void
    {
        $this->extensionConfigurationStub = $this->createStub(ExtensionConfiguration::class);
        $this->languageServiceStub = $this->createStub(LanguageService::class);

        $this->subject = new LinkMatomoProvider($this->extensionConfigurationStub, $this->languageServiceStub);
    }

    /**
     * @test
     */
    public function getTitleReturnsTranslatedTitleCorrectly(): void
    {
        $this->languageServiceStub
            ->method('sL')
            ->with(Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.linkMatomo.buttonText')
            ->willReturn('Matomo link');

        self::assertSame('Matomo link', $this->subject->getTitle());
    }

    /**
     * @test
     */
    public function getLinkReturnsConfiguredLinkCorrectly(): void
    {
        $this->extensionConfigurationStub
            ->method('get')
            ->with(Extension::KEY, 'url')
            ->willReturn('https://example.net/');

        self::assertSame('https://example.net/', $this->subject->getLink());
    }

    /**
     * @test
     */
    public function getTargetReturnsBlankTarget(): void
    {
        self::assertSame('_blank', $this->subject->getTarget());
    }
}