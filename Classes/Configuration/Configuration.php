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
        private readonly string $siteIdentifier,
        private readonly string $siteTitle,
        private readonly string $url,
        private readonly int $idSite,
        private readonly string $tokenAuth,
        private readonly array $activeWidgets,
        private readonly array $customDimensions,
        private readonly string $pagesNotFoundTemplate
    ) {
    }

    public function getSiteIdentifier(): string
    {
        return $this->siteIdentifier;
    }

    public function getSiteTitle(): string
    {
        return $this->siteTitle;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getIdSite(): int
    {
        return $this->idSite;
    }

    public function getTokenAuth(): string
    {
        return $this->tokenAuth;
    }

    public function isWidgetActive(string $widgetIdentifier): bool
    {
        return \in_array($widgetIdentifier, $this->activeWidgets, true);
    }

    /**
     * @return CustomDimension[]
     */
    public function getCustomDimensions(): array
    {
        return $this->customDimensions;
    }

    public function getPagesNotFoundTemplate(): string
    {
        return $this->pagesNotFoundTemplate;
    }
}
