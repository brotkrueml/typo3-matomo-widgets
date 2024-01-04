<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\ContentSecurityPolicy;

use Brotkrueml\MatomoWidgets\Configuration\Configuration;
use Brotkrueml\MatomoWidgets\Configuration\Configurations;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Directive;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Event\PolicyMutatedEvent;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Scope;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\UriValue;

/**
 * Event listener which extends the Content Security Policy for the configured Matomo servers
 * @internal
 */
final class ExtendPolicyForMatomoServers
{
    private const WIDGETS_TO_CONSIDER = [
        'browserPlugins',
        'countries',
    ];

    public function __construct(
        private readonly Configurations $configurations,
    ) {}

    public function __invoke(PolicyMutatedEvent $event): void
    {
        if ($event->scope !== Scope::backend()) {
            return;
        }

        $uriValues = [];
        foreach ($this->configurations as $configuration) {
            if ($this->shouldAddUri($configuration)) {
                $uriValues[] = new UriValue((new Uri($configuration->url))->getHost());
            }
        }

        if ($uriValues !== []) {
            $event->getCurrentPolicy()->extend(Directive::ImgSrc, ...$uriValues);
        }
    }

    private function shouldAddUri(Configuration $configuration): bool
    {
        foreach (self::WIDGETS_TO_CONSIDER as $widgetIdentifier) {
            if ($configuration->isWidgetActive($widgetIdentifier)) {
                return true;
            }
        }

        return false;
    }
}
