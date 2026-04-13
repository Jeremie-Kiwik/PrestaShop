<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search\Filters;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ExtraPropertyDefinitionGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Default search filter configuration for the Extra Fields management grid.
 *
 * Holds the default sorting, pagination and filter state used when the grid
 * is first displayed or when no persisted filter state is found in session.
 */
class ExtraFieldFilters extends Filters
{
    /** @var string */
    protected $filterId = ExtraPropertyDefinitionGridDefinitionFactory::GRID_ID;

    /**
     * {@inheritdoc}
     */
    public static function getDefaults(): array
    {
        return [
            'limit' => 20,
            'offset' => 0,
            'orderBy' => 'id_extra_property_definition',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
