<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\ContentSecurityPolicy;

use Brotkrueml\MatomoWidgets\Configuration\Configuration;
use Brotkrueml\MatomoWidgets\Configuration\Configurations;
use Brotkrueml\MatomoWidgets\ContentSecurityPolicy\ExtendPolicyForMatomoServers;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Directive;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Event\PolicyMutatedEvent;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Policy;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Scope;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\SourceKeyword;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\UriValue;

#[CoversClass(ExtendPolicyForMatomoServers::class)]
final class ExtendPolicyForMatomoServersTest extends TestCase
{
    protected function setUp(): void
    {
        if ((new Typo3Version())->getMajorVersion() < 12) {
            self::markTestSkipped('Feature is only available in TYPO3 v12+');
        }
    }

    #[Test]
    public function whenNotInFrontendScopeThePolicyIsNotChanged(): void
    {
        $subject = new ExtendPolicyForMatomoServers($this->getDefaultConfigurations());

        $defaultPolicy = $this->getDefaultPolicy();
        $event = new PolicyMutatedEvent(Scope::frontend(), $defaultPolicy, $defaultPolicy);

        $subject->__invoke($event);

        self::assertSame($event->getCurrentPolicy(), $defaultPolicy);
    }

    #[Test]
    public function whenInBackendThePolicyIsExtended(): void
    {
        $subject = new ExtendPolicyForMatomoServers($this->getDefaultConfigurations());

        $defaultPolicy = $this->getDefaultPolicy();
        $event = new PolicyMutatedEvent(Scope::backend(), $defaultPolicy, $defaultPolicy);

        $subject->__invoke($event);

        self::assertTrue($event->getCurrentPolicy()->containsDirective(Directive::ImgSrc, new UriValue('extend1.example.org')));
        self::assertTrue($event->getCurrentPolicy()->containsDirective(Directive::ImgSrc, new UriValue('extend2.example.org')));
        self::assertFalse($event->getCurrentPolicy()->containsDirective(Directive::ImgSrc, new UriValue('noextend.example.org')));
    }

    #[Test]
    public function wheninBackendAndNoConsideredWidgetsIsActiveThePolicyIsNotExtended(): void
    {
        $subject = new ExtendPolicyForMatomoServers(new Configurations([
            new Configuration(
                'one_more_site',
                '',
                'https://noextend.example.org/',
                3,
                '',
                ['visits'],
                [],
                '',
            ),
        ]));

        $defaultPolicy = $this->getDefaultPolicy();
        $event = new PolicyMutatedEvent(Scope::backend(), $defaultPolicy, $defaultPolicy);

        $subject->__invoke($event);

        self::assertFalse($event->getCurrentPolicy()->containsDirective(Directive::ImgSrc, new UriValue('noextend.example.org')));
    }

    private function getDefaultConfigurations(): Configurations
    {
        return new Configurations([
            new Configuration(
                'some_site',
                '',
                'https://extend1.example.org/',
                1,
                '',
                ['browserPlugins', 'countries'],
                [],
                '',
            ),
            new Configuration(
                'another_site',
                '',
                'https://extend2.example.org/',
                2,
                '',
                ['browserPlugins'],
                [],
                '',
            ),
            new Configuration(
                'one_more_site',
                '',
                'https://noextend.example.org/',
                3,
                '',
                ['actionsPerDay', 'actionsPerMonth', 'annotations', 'createAnnotation', 'bounceRate', 'browsers', 'campaigns', 'contentNames', 'contentPieces', 'javaScriptErrors', 'linkMatomo', 'osFamilies', 'pagesNotFound', 'siteSearchKeywords', 'siteSearchNoResultKeywords', 'visitsPerDay', 'visitsPerMonth'],
                [],
                '',
            ),
        ]);
    }

    private function getDefaultPolicy(): Policy
    {
        return (new Policy())
            ->set(Directive::DefaultSrc, SourceKeyword::self)
            ->extend(Directive::ImgSrc, new UriValue('predefined.example.com'));
    }
}
