<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\ExtraProperty\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ExtraPropertyDomainException;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Query\GetExtraPropertyDefinitionForEditing;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\QueryHandler\GetExtraPropertyDefinitionForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\QueryResult\ExtraPropertyDefinitionInfo;
use PrestaShop\PrestaShop\Core\ExtraProperty\Registry\ExtraPropertyRegistryInterface;

/**
 * Loads a single extra property definition by primary key for editing in the BO form.
 *
 * Uses ExtraPropertyRegistryInterface::getDefinitionById() which bypasses any in-memory
 * collection cache and queries the DB directly.
 */
#[AsQueryHandler]
class GetExtraPropertyDefinitionForEditingHandler implements GetExtraPropertyDefinitionForEditingHandlerInterface
{
    public function __construct(protected readonly ExtraPropertyRegistryInterface $registry)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetExtraPropertyDefinitionForEditing $query): ExtraPropertyDefinitionInfo
    {
        $row = $this->registry->getDefinitionById($query->getId());

        if (null === $row) {
            throw new ExtraPropertyDomainException(
                sprintf('Extra property definition with id %d not found.', $query->getId()),
            );
        }

        return ExtraPropertyDefinitionInfo::fromRow($row);
    }
}
