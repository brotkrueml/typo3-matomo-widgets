<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Widgets\Decorator;

/**
 * @internal
 */
final class BrowserPluginIconDecorator implements DecoratorInterface
{
    private readonly string $baseUrl;

    public function __construct(string $url)
    {
        $this->baseUrl = $this->normaliseUrl($url);
    }

    private function normaliseUrl(string $url): string
    {
        $url = \str_replace('index.php', '', $url);

        return \rtrim($url, '/') . '/';
    }

    public function decorate(string $value): string
    {
        if ($value === '') {
            return '';
        }

        $imageUrl = $this->baseUrl . $value;
        if (! \filter_var($imageUrl, \FILTER_VALIDATE_URL)) {
            return '';
        }

        return '<img src="' . $imageUrl . '" width="16" alt="">';
    }

    public function isHtmlOutput(): bool
    {
        return true;
    }
}
