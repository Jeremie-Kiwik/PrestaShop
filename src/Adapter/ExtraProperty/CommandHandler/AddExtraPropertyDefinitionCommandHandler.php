<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\ExtraProperty\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\AddExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\CommandHandler\AddExtraPropertyDefinitionCommandHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ExtraPropertyDomainException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Registry\ExtraPropertyRegistryInterface;
use Throwable;

/**
 * Registers a new core extra property definition via ExtraPropertyRegistryInterface::register().
 *
 * The registry ensures the physical SQL column is created in the entity's extra table.
 * Module name is always set to null (translated to '' in the registry) to mark the field as core-managed.
 */
#[AsCommandHandler]
class AddExtraPropertyDefinitionCommandHandler implements AddExtraPropertyDefinitionCommandHandlerInterface
{
    public function __construct(protected readonly ExtraPropertyRegistryInterface $registry)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddExtraPropertyDefinitionCommand $command): int
    {
        // Build the options array expected by ExtraPropertyRegistry::register().
        $options = [
            'type' => $command->getFieldType(),
            'scope' => $command->getFieldScope(),
            'sql_index' => $command->getSqlIndex(),
            'display_front' => $command->isDisplayFront(),
            'display_api' => $command->isDisplayApi(),
            'display_bo' => $command->isDisplayBo(),
            'display_grid' => $command->isDisplayGrid(),
        ];

        if (null !== $command->getTitleWording()) {
            $options['title_wording'] = $command->getTitleWording();
        }
        if (null !== $command->getTitleDomain()) {
            $options['title_domain'] = $command->getTitleDomain();
        }
        if (null !== $command->getDescriptionWording()) {
            $options['description_wording'] = $command->getDescriptionWording();
        }
        if (null !== $command->getDescriptionDomain()) {
            $options['description_domain'] = $command->getDescriptionDomain();
        }
        if (null !== $command->getGridPosition()) {
            $options['grid_position'] = $command->getGridPosition();
        }
        if (null !== $command->getValidator()) {
            $options['validator'] = $command->getValidator();
        }
        if (null !== $command->getSymfonyFieldType()) {
            $options['symfony_field_type'] = $command->getSymfonyFieldType();
        }

        try {
            // null module name = core field (stored as '' in the registry).
            $registered = $this->registry->register(
                $command->getEntityName(),
                $command->getFieldName(),
                null,
                $options,
            );
        } catch (Throwable $e) {
            throw new ExtraPropertyDomainException(
                sprintf('Failed to register extra property "%s.%s": %s', $command->getEntityName(), $command->getFieldName(), $e->getMessage()),
                0,
                $e,
            );
        }

        if (!$registered) {
            throw new ExtraPropertyDomainException(
                sprintf('Could not register extra property "%s.%s" (possible duplicate or invalid options).', $command->getEntityName(), $command->getFieldName()),
            );
        }

        // Retrieve the newly created definition's primary key.
        $definition = $this->registry->getByEntityAndFieldName(
            $command->getEntityName(),
            $command->getFieldName(),
            $command->getFieldScope(),
        );

        if (null === $definition || !isset($definition['id_extra_property_definition'])) {
            throw new ExtraPropertyDomainException(
                sprintf('Extra property "%s.%s" was registered but could not be retrieved.', $command->getEntityName(), $command->getFieldName()),
            );
        }

        return (int) $definition['id_extra_property_definition'];
    }
}
