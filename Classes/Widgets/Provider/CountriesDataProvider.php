<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Widgets\Provider;

use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

/**
 * @internal
 */
final class CountriesDataProvider extends GenericTableDataProvider
{
    public function getRows(): array
    {
        $userLanguage = $this->getBackendUser()->user['lang'] ?? 'en';

        $parameters = \array_merge(
            $this->parameters,
            // We pass the language, so we have translated countries
            [
                'language' => $userLanguage === 'default' ? 'en' : $userLanguage,
            ],
        );

        return $this->repository->send($this->connectionConfiguration, $this->method, new ParameterBag($parameters));
    }

    private function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
