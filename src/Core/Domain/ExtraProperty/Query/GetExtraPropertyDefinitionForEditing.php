<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Query;

/**
 * Query to load one extra property definition by its primary key for editing.
 *
 * The handler returns an ExtraPropertyDefinitionInfo value object populated
 * from the extra_property_definition registry table row.
 */
class GetExtraPropertyDefinitionForEditing
{
    /**
     * @param int $id Primary key of the extra property definition
     */
    public function __construct(protected readonly int $id)
    {
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
