<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Parameter;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

final class LanguageParameterResolver implements ParameterResolverInterface
{
    public function resolve(): string
    {
        $backendUser = $this->getBackendUser();
        if ($backendUser === null) {
            return '';
        }

        return $backendUser->uc['lang'] ?? '';
    }

    private function getBackendUser(): ?BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'] ?? null;
    }
}
