<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\ExtraProperty\CommandHandler;

use PrestaShop\PrestaShop\Adapter\ExtraProperty\Repository\ExtraPropertyDefinitionRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\EditExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\CommandHandler\EditExtraPropertyDefinitionCommandHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ExtraPropertyDomainException;
use Throwable;

/**
 * Updates metadata fields of an existing extra property definition via a direct SQL UPDATE.
 *
 * Structural fields (entity_name, field_name, field_type, field_scope, storage_column_name,
 * sql_index) are never modified here — changing those would require DDL operations.
 * The handler validates that the target definition exists before updating.
 */
#[AsCommandHandler]
class EditExtraPropertyDefinitionCommandHandler implements EditExtraPropertyDefinitionCommandHandlerInterface
{
    public function __construct(protected readonly ExtraPropertyDefinitionRepository $repository)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(EditExtraPropertyDefinitionCommand $command): void
    {
        $id = $command->getId();

        // Verify the definition exists before updating.
        $existing = $this->repository->findById($id);
        if (null === $existing) {
            throw new ExtraPropertyDomainException(
                sprintf('Extra property definition with id %d not found.', $id),
            );
        }

        // Build only the metadata columns that are allowed to change.
        // Note: date_upd is intentionally omitted — the column may not be present in all
        // installations (older schemas created before it was added to the structure).
        $data = [
            'title_wording' => $command->getTitleWording() ?? '',
            'title_domain' => $command->getTitleDomain() ?? '',
            'description_wording' => $command->getDescriptionWording() ?? '',
            'description_domain' => $command->getDescriptionDomain() ?? '',
            'display_front' => $command->isDisplayFront() ? 1 : 0,
            'display_api' => $command->isDisplayApi() ? 1 : 0,
            'display_bo' => $command->isDisplayBo() ? 1 : 0,
            'display_grid' => $command->isDisplayGrid() ? 1 : 0,
            'grid_position' => $command->getGridPosition(),
            'validator' => $command->getValidator() ?? '',
            'symfony_field_type' => $command->getSymfonyFieldType() ?? '',
        ];

        try {
            $result = $this->repository->save($data, $id);
        } catch (Throwable $e) {
            throw new ExtraPropertyDomainException(
                sprintf('Failed to update extra property definition %d: %s', $id, $e->getMessage()),
                0,
                $e,
            );
        }

        if (false === $result) {
            throw new ExtraPropertyDomainException(
                sprintf('Failed to update extra property definition %d.', $id),
            );
        }
    }
}
