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
    private LanguageService $languageService;
    private string $link;

    public function __construct(LanguageService $languageService, string $link)
    {
        $this->languageService = $languageService;
        $this->link = $link;
    }

    public function getTitle(): string
    {
        return $this->languageService->sL(Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.linkMatomo.buttonText');
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getTarget(): string
    {
        return '_blank';
    }
}
