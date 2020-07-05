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
 * Get metrics for the VisitsSummary module.
 * The method names correspond to the VisitsSummary API methods.
 *
 * @see https://developer.matomo.org/api-reference/reporting-api#VisitsSummary
 */
class VisitsSummaryRepository
{
    /** @var MatomoConnector */
    private $matomoConnector;

    public function __construct(MatomoConnector $matomoConnector)
    {
        $this->matomoConnector = $matomoConnector;
    }

    public function get(string $period, string $date, string $columns = ''): array
    {
        $parameters = [
            'period' => $period,
            'date' => $date,
        ];

        if ($columns) {
            $parameters['columns'] = $columns;
        }

        return $this->matomoConnector->callApi('VisitsSummary.get', $parameters);
    }

    public function getActions(string $period, string $date): array
    {
        return $this->retrieve('getActions', $period, $date);
    }

    public function getVisits(string $period, string $date): array
    {
        return $this->retrieve('getVisits', $period, $date);
    }

    private function retrieve(string $method, string $period, string $date): array
    {
        $parameters = [
            'period' => $period,
            'date' => $date,
        ];

        return $this->matomoConnector->callApi('VisitsSummary.' . $method, $parameters);
    }
}
