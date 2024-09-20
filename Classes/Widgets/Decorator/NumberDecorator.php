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
use TYPO3\CMS\Core\Localization\LanguageService;

/**
 * @internal
 */
final class NumberDecorator implements DecoratorInterface
{
    public function decorate(string $value): string
    {
        return \number_format(
            (int) $value,
            0,
            '',
            $this->getLanguageService()->sL(Extension::LANGUAGE_PATH_DASHBOARD . ':thousandsSeparator'),
        );
    }

    public function isHtmlOutput(): bool
    {
        return false;
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
