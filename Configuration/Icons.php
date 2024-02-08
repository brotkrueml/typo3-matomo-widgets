<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\MatomoWidgets\Extension;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'content-bounce-rate' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:' . Extension::KEY . '/Resources/Public/Icons/content-bounce-rate.svg',
    ],
    'content-widget-matomo' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:' . Extension::KEY . '/Resources/Public/Icons/content-widget-matomo.svg',
    ],
];
