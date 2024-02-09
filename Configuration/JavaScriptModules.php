<?php

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\MatomoWidgets\Extension;

return [
    'dependencies' => ['core', 'backend'],
    'imports' => [
        Extension::JS_IMPORT_PREFIX . '/' => 'EXT:' . Extension::KEY . '/Resources/Public/JavaScript/',
    ],
];
