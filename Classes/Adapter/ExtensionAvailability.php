<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Adapter;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * @internal
 */
class ExtensionAvailability
{
    public function isMatomoIntegrationAvailable(): bool
    {
        return ExtensionManagementUtility::isLoaded('matomo_integration');
    }
}
