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
final class JavaScriptErrorDecorator implements DecoratorInterface
{
    /**
     * @var string
     */
    private $siteIdentifier;

    public function __construct(string $siteIdentifier)
    {
        $this->siteIdentifier = $siteIdentifier;
    }

    public function decorate(string $value): string
    {
        if ($value === '') {
            return '';
        }

        return \sprintf(
            '<a class="js-matomo-widgets-javascript-error-message" href="#" data-site-identifier="%s">%s</a>',
            \htmlspecialchars($this->siteIdentifier),
            \htmlspecialchars($value)
        );
    }

    public function isHtmlOutput(): bool
    {
        return true;
    }
}
