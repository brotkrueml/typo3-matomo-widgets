<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Configuration;

use Brotkrueml\MatomoWidgets\Domain\Entity\CustomDimension;

/**
 * @internal
 */
final class Configuration
{
    /**
     * @param string[] $activeWidgets
     * @param CustomDimension[] $customDimensions
     */
    public function __construct(
        public readonly string $siteIdentifier,
        public readonly string $siteTitle,
        public readonly string $url,
        public readonly int $idSite,
        #[\SensitiveParameter]
        public readonly string $tokenAuth,
        public readonly array $activeWidgets,
        public readonly array $customDimensions,
        public readonly string $pagesNotFoundTemplate,
    ) {
    }

    public function isWidgetActive(string $widgetIdentifier): bool
    {
        return \in_array($widgetIdentifier, $this->activeWidgets, true);
    }
}
