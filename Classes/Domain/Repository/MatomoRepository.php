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
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;

/**
 * Retrieved data from the Matomo Reporting API dependent on the method
 * @see https://developer.matomo.org/api-reference/reporting-api
 */
class MatomoRepository implements RepositoryInterface
{
    /**
     * @var MatomoConnector
     */
    private $connector;

    public function __construct(MatomoConnector $connector)
    {
        $this->connector = $connector;
    }

    public function find(string $method, ParameterBag $parameterBag): array
    {
        return $this->connector->callApi($method, $parameterBag);
    }
}
