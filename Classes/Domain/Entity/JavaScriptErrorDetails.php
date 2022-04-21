<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Domain\Entity;

use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * @internal
 */
final class JavaScriptErrorDetails
{
    private int $lastAppearance = 0;
    /**
     * @var array<string, BrowserCount>
     */
    private array $browsers = [];
    /**
     * @var array<string, UrlCount>
     */
    private array $urls = [];
    /**
     * @var array<string, ScriptCount>
     */
    private array $scripts = [];

    public function compareAndStoreLastAppearance(int $timestamp): void
    {
        if ($timestamp > $this->lastAppearance) {
            $this->lastAppearance = $timestamp;
        }
    }

    public function incrementBrowserCount(string $name, string $icon): void
    {
        if (! isset($this->browsers[$name])) {
            $this->browsers[$name] = new BrowserCount($name, $icon);
        }
        $this->browsers[$name]->incrementHit();
    }

    public function incrementUrlCount(string $url): void
    {
        if (! isset($this->urls[$url])) {
            $this->urls[$url] = new UrlCount($url);
        }
        $this->urls[$url]->incrementHits();
    }

    public function incrementScriptCount(string $script): void
    {
        if (! isset($this->scripts[$script])) {
            $this->scripts[$script] = new ScriptCount($script);
        }
        $this->scripts[$script]->incrementHits();
    }

    public function getLastAppearance(): string
    {
        return BackendUtility::datetime($this->lastAppearance);
    }

    /**
     * @return BrowserCount[]
     */
    public function getBrowsers(): array
    {
        $browsers = \array_values($this->browsers);
        \usort($browsers, static fn (BrowserCount $a, BrowserCount $b): int => $b->getHits() <=> $a->getHits());

        return \array_values($browsers);
    }

    public function getBrowsersCount(): int
    {
        return \count($this->browsers);
    }

    /**
     * @return UrlCount[]
     */
    public function getUrls(): array
    {
        $urls = \array_values($this->urls);
        \usort($urls, static fn (UrlCount $a, UrlCount $b): int => $b->getHits() <=> $a->getHits());

        return \array_values($urls);
    }

    public function getUrlsCount(): int
    {
        return \count($this->urls);
    }

    /**
     * @return ScriptCount[]
     */
    public function getScripts(): array
    {
        $scripts = \array_values($this->scripts);
        \usort($scripts, static fn (ScriptCount $a, ScriptCount $b): int => $b->getHits() <=> $a->getHits());

        return \array_values($scripts);
    }

    public function getScriptsCount(): int
    {
        return \count($this->scripts);
    }
}
