<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Configuration;

final class Configuration
{
    /** @var string */
    private $siteIdentifier;

    /** @var string */
    private $siteTitle;

    /** @var string */
    private $url;

    /** @var int */
    private $idSite;

    /** @var string */
    private $tokenAuth;

    /** @var array<string,bool> */
    private $widgets;

    public function __construct(string $siteIdentifier, string $siteTitle, string $url, int $idSite, string $tokenAuth, array $widgets)
    {
        $this->siteIdentifier = $siteIdentifier;
        $this->siteTitle = $siteTitle;
        $this->url = $url;
        $this->idSite = $idSite;
        $this->tokenAuth = $tokenAuth;
        $this->widgets = $widgets;
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

    public function isWidgetEnabled(string $widgetConfigurationKey): bool
    {
        return $this->widgets[$widgetConfigurationKey] ?? false;
    }
}
