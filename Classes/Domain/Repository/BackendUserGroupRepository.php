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
class BackendUserGroupRepository
{
    private const TABLE = 'be_groups';

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
     * @return iterable<array{uid: int, availableWidgets: string}>
     */
    public function findAll(): iterable
    {
        $statement = $this->queryBuilder
            ->select('uid', 'availableWidgets')
            ->from(self::TABLE)
            ->execute();

        while ($row = $statement->fetch()) {
            yield $row;
        }
    }

    /**
     * @param list<string> $availableWidgets
     */
    public function updateAvailableWidgets(int $uid, array $availableWidgets): void
    {
        $this->queryBuilder
            ->update(self::TABLE)
            ->set('availableWidgets', \implode(',', $availableWidgets))
            ->where(
                $this->queryBuilder->expr()->eq('uid', $this->queryBuilder->createNamedParameter($uid))
            )
            ->execute();
    }
}
