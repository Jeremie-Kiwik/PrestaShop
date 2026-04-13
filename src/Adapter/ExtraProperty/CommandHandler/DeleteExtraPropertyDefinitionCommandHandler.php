<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\ExtraProperty\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\DeleteExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\CommandHandler\DeleteExtraPropertyDefinitionCommandHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ExtraPropertyDomainException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Registry\ExtraPropertyRegistryInterface;
use Throwable;

/**
 * Unregisters an extra property definition via ExtraPropertyRegistryInterface::unregisterById().
 *
 * When $dropColumn is true (the default when triggered from the BO), the physical SQL column
 * is also dropped from the entity's extra table. Cache is invalidated by the registry decorator.
 */
#[AsCommandHandler]
class DeleteExtraPropertyDefinitionCommandHandler implements DeleteExtraPropertyDefinitionCommandHandlerInterface
{
    public function __construct(protected readonly ExtraPropertyRegistryInterface $registry)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DeleteExtraPropertyDefinitionCommand $command): void
    {
        // Verify the definition exists before attempting to unregister.
        $existing = $this->registry->getDefinitionById($command->getId());
        if (null === $existing) {
            throw new ExtraPropertyDomainException(
                sprintf('Extra property definition with id %d not found.', $command->getId()),
            );
        }

        try {
            $success = $this->registry->unregisterById($command->getId(), $command->shouldDropColumn());
        } catch (Throwable $e) {
            throw new ExtraPropertyDomainException(
                sprintf('Failed to delete extra property definition %d: %s', $command->getId(), $e->getMessage()),
                0,
                $e,
            );
        }

        if (!$success) {
            throw new ExtraPropertyDomainException(
                sprintf('Failed to delete extra property definition %d.', $command->getId()),
            );
        }
    }
}
