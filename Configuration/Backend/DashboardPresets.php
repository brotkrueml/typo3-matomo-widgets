<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Brotkrueml\MatomoWidgets\Adapter\ExtensionAvailability;
use Brotkrueml\MatomoWidgets\Backend\DashboardPresetsProvider;
use Brotkrueml\MatomoWidgets\Configuration\ConfigurationFinder;
use TYPO3\CMS\Core\Core\Environment;

$configurations = ConfigurationFinder::buildConfigurations(
    Environment::getConfigPath(),
    (new ExtensionAvailability())->isMatomoIntegrationAvailable()
);

return (new DashboardPresetsProvider($configurations))
    ->getPresets();
