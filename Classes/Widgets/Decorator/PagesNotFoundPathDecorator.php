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
final class PagesNotFoundPathDecorator implements DecoratorInterface
{
    private const LLL_REFERRER = Extension::LANGUAGE_PATH_DASHBOARD . ':referrer';

    /**
     * @var string
     */
    private $regex;

    /**
     * @var bool
     */
    private $isPathBeforeReferrer;

    public function __construct(string $template)
    {
        $pathPosition = \strpos($template, '{path}');
        $referrerPosition = \strpos($template, '{referrer}');
        if ($referrerPosition === false) {
            $this->isPathBeforeReferrer = true;
        } else {
            $this->isPathBeforeReferrer = $pathPosition < $referrerPosition;
        }

        $replacements = [
            '\{statusCode\}' => '404',
            '\{path\}' => '(.*?)',
            '\{referrer\}' => '(.*?)',
        ];
        $regex =
            '`^' . \str_replace(\array_keys($replacements), \array_values($replacements), \preg_quote($template)) . '$`';

        // The following replacement necessary to avoid not matching when the regex expects
        // an empty space (for example, when the referrer is empty using this template:
        // Error 404 | Path = {path} | Referrer = {referrer}
        // Matomo returns the value trimmed!
        $this->regex = \str_replace(' (.*?)', '(.*?)', $regex);
    }

    public function decorate(string $value): string
    {
        if ($value === '') {
            return '';
        }

        return $this->formatValue($value);
    }

    private function formatValue(string $value): string
    {
        $value = \trim($value);
        if (! \preg_match($this->regex, $value, $matches)) {
            return $value;
        }

        if ($this->isPathBeforeReferrer) {
            $path = \trim($matches[1] ?? '');
            $referrer = \trim($matches[2] ?? '');
        } else {
            $path = \trim($matches[2] ?? '');
            $referrer = \trim($matches[1] ?? '');
        }

        if ($path === '') {
            return $value;
        }

        return $path . (
            $referrer !== ''
                ? '<br><b>' . $this->getLanguageService()->sL(self::LLL_REFERRER) . ':</b> ' . $referrer
                : ''
        );
    }

    public function isHtmlOutput(): bool
    {
        return true;
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
