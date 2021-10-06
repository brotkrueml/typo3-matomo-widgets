<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

return [
    'matomo_widgets_create_annotation' => [
        'path' => '/matomo-widgets/create-annotation',
        'methods' => ['POST'],
        'target' => Brotkrueml\MatomoWidgets\Controller\CreateAnnotationController::class,
    ],
];
