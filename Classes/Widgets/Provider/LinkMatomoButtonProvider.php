<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Widgets\Provider;

use Brotkrueml\MatomoWidgets\Extension;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Dashboard\Widgets\ButtonProviderInterface;

/**
 * @internal
 */
final class LinkMatomoButtonProvider implements ButtonProviderInterface
{
    public function __construct(
        private readonly string $link,
    ) {}

    public function getTitle(): string
    {
        return $this->getLanguageService()->sL(Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.linkMatomo.buttonText');
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getTarget(): string
    {
        return '_blank';
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
