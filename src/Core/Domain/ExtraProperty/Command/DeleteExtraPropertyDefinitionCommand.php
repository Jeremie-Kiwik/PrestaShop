<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command;

/**
 * Command to unregister (delete) an extra property definition by its primary key.
 *
 * When $dropColumn is true (default), the physical SQL column in the corresponding
 * *_extra / *_extra_lang / *_extra_shop table is also dropped. Set it to false to
 * remove only the registry row while keeping the data column (useful for soft-cleanup).
 */
class DeleteExtraPropertyDefinitionCommand
{
    /**
     * @param int $id Primary key of the extra property definition to delete
     * @param bool $dropColumn Whether to also drop the physical SQL column from the extra table
     */
    public function __construct(
        protected readonly int $id,
        protected readonly bool $dropColumn = true,
    ) {
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function shouldDropColumn(): bool
    {
        return $this->dropColumn;
    }
}
