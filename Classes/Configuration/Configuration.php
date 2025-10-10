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
final readonly class Configuration
{
    /**
     * @param string[] $activeWidgets
     * @param CustomDimension[] $customDimensions
     */
    public function __construct(
        public string $siteIdentifier,
        public string $siteTitle,
        public string $url,
        public int $idSite,
        #[\SensitiveParameter]
        public string $tokenAuth,
        public array $activeWidgets,
        public array $customDimensions,
        public string $pagesNotFoundTemplate,
    ) {}

    public function isWidgetActive(string $widgetIdentifier): bool
    {
        return \in_array($widgetIdentifier, $this->activeWidgets, true);
    }
}
