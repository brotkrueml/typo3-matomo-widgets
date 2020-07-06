<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Domain\Repository;

use Brotkrueml\MatomoWidgets\Connection\MatomoConnector;

/**
 * Get metrics for the Referrers module.
 * The method names correspond to the DevicesDetection API methods.
 *
 * @see https://developer.matomo.org/api-reference/reporting-api#Referrers
 */
class ReferrersRepository
{
    /** @var MatomoConnector */
    private $matomoConnector;

    public function __construct(MatomoConnector $matomoConnector)
    {
        $this->matomoConnector = $matomoConnector;
    }

    public function getCampaigns(string $period, string $date): array
    {
        $parameters = [
            'period' => $period,
            'date' => $date,
            'filter_sort_column' => 'nb_visits',
            'filter_sort_order' => 'desc',
        ];

        return $this->matomoConnector->callApi('Referrers.getCampaigns', $parameters);
    }
}
