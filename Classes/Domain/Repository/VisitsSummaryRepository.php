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

class VisitsSummaryRepository
{
    /** @var MatomoConnector */
    private $matomoConnector;

    public function __construct(MatomoConnector $matomoConnector)
    {
        $this->matomoConnector = $matomoConnector;
    }

    public function getVisits(string $period, string $date): array
    {
        $parameters = [
            'period' => $period,
            'date' => $date,
        ];

        return $this->matomoConnector->callApi('VisitsSummary.getVisits', $parameters);
    }
}
