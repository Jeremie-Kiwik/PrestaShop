<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Builds search and count DBAL queries for the Extra Property Definition grid.
 *
 * The grid lists all rows from the extra_property_definition registry table,
 * covering both core (_core) and module-managed fields.
 */
final class ExtraPropertyDefinitionQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /** @var string Fully qualified table name (with prefix) */
    private string $definitionTable;

    /**
     * @param Connection $connection
     * @param string $dbPrefix Database table prefix
     */
    public function __construct(Connection $connection, string $dbPrefix)
    {
        parent::__construct($connection, $dbPrefix);

        $this->definitionTable = $dbPrefix . 'extra_property_definition';
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(?SearchCriteriaInterface $searchCriteria = null): QueryBuilder
    {
        $qb = $this->buildBaseQuery($searchCriteria);

        return $qb
            ->select(
                'epd.id_extra_property_definition',
                'epd.entity_name',
                'epd.module_name',
                'epd.field_name',
                'epd.storage_column_name',
                'epd.field_type',
                'epd.field_scope',
                'epd.display_front',
                'epd.display_api',
                'epd.display_bo',
                'epd.display_grid',
            )
            ->orderBy(
                sprintf('`%s`', $searchCriteria->getOrderBy()),
                $searchCriteria->getOrderWay(),
            )
            ->setFirstResult($searchCriteria->getOffset() ?? 0)
            ->setMaxResults($searchCriteria->getLimit());
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(?SearchCriteriaInterface $searchCriteria = null): QueryBuilder
    {
        $qb = $this->buildBaseQuery($searchCriteria);

        return $qb->select('COUNT(epd.id_extra_property_definition)');
    }

    /**
     * Builds the WHERE clause from active filter criteria.
     *
     * Exact match is used for numeric ID and ENUM fields (field_type, field_scope);
     * LIKE is used for free-text string fields.
     *
     * @param SearchCriteriaInterface $criteria
     *
     * @return QueryBuilder
     */
    private function buildBaseQuery(SearchCriteriaInterface $criteria): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->from($this->definitionTable, 'epd');

        foreach ($criteria->getFilters() as $filterName => $value) {
            if ('' === $value || null === $value) {
                continue;
            }

            // Exact match for the primary key.
            if ('id_extra_property_definition' === $filterName) {
                $qb->andWhere('epd.id_extra_property_definition = :id_extra_property_definition');
                $qb->setParameter('id_extra_property_definition', (int) $value);

                continue;
            }

            // Exact match for ENUM fields (values are controlled by the filter form).
            if (in_array($filterName, ['field_type', 'field_scope'], true)) {
                $qb->andWhere(sprintf('epd.`%s` = :%s', $filterName, $filterName));
                $qb->setParameter($filterName, $value);

                continue;
            }

            // LIKE search for free-text columns.
            $qb->andWhere(sprintf('epd.`%s` LIKE :%s', $filterName, $filterName));
            $qb->setParameter($filterName, '%' . $value . '%');
        }

        return $qb;
    }
}
