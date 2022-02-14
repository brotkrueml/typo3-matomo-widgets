<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Domain\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal
 */
class DashboardRepository
{
    private const TABLE = 'be_dashboards';

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    public function __construct()
    {
        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $this->queryBuilder = $connectionPool->getQueryBuilderForTable(self::TABLE);
    }

    /**
     * @return iterable<array{identifier: string, widgets: string}>
     */
    public function findAll(): iterable
    {
        $statement = $this->queryBuilder
            ->select('identifier', 'widgets')
            ->from(self::TABLE)
            ->execute();

        while ($row = $statement->fetch()) {
            yield $row;
        }
    }

    public function updateWidgetConfig(string $identifier, array $widgets): void
    {
        $this->queryBuilder
            ->update(self::TABLE)
            ->set('widgets', \json_encode($widgets))
            ->where(
                $this->queryBuilder->expr()->eq('identifier', $this->queryBuilder->createNamedParameter($identifier))
            )
            ->execute();
    }
}
