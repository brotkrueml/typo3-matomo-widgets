<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Widgets\Decorator;

use Brotkrueml\MatomoWidgets\Extension;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class CountryFlagDecorator implements DecoratorInterface
{
    /**
     * @var string
     */
    private $baseUrl;

    public function __construct(ExtensionConfiguration $extensionConfiguration)
    {
        $this->baseUrl = $this->normaliseUrl($extensionConfiguration->get(Extension::KEY)['url']);
    }

    private function normaliseUrl(string $url): string
    {
        $url = \str_replace('index.php', '', $url);
        $url = \rtrim($url, '/') . '/';

        return $url;
    }

    public function decorate(string $value): string
    {
        if (!$value) {
            return '';
        }

        $imageUrl = $this->baseUrl . $value;
        if (!\filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return '';
        }

        return \sprintf('<img src="%s" width="24" alt="" class="matomo-widgets__country-flag__image">', $imageUrl);
    }

    public function isHtmlOutput(): bool
    {
        return true;
    }
}
