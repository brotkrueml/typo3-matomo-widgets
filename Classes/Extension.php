<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets;

/**
 * @internal
 */
final readonly class Extension
{
    public const KEY = 'matomo_widgets';

    public const JS_IMPORT_PREFIX = '@brotkrueml/matomo-widgets';

    private const LANGUAGE_PATH = 'LLL:EXT:' . self::KEY . '/Resources/Private/Language/';
    public const LANGUAGE_PATH_DASHBOARD = self::LANGUAGE_PATH . 'Dashboard.xlf';
    public const LANGUAGE_PATH_SITECONF = self::LANGUAGE_PATH . 'SiteConfiguration.xlf';

    public const ADDITIONAL_CONFIG_PATH_SEGMENT = self::KEY;

    // Placeholders: site identifier / widget service ID suffix
    public const WIDGET_IDENTIFIER_TEMPLATE = 'matomo_widgets.%s.%s';
}
