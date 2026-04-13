<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\AddExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\EditExtraPropertyDefinitionCommand;

/**
 * Persists extra property definition form data via CQRS commands.
 *
 * On create: dispatches AddExtraPropertyDefinitionCommand (module_name forced to '' = core).
 * On update: dispatches EditExtraPropertyDefinitionCommand with metadata fields only.
 */
final class ExtraFieldFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(private readonly CommandBusInterface $commandBus)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @return int The new definition's primary key
     */
    public function create(array $data): int
    {
        $command = new AddExtraPropertyDefinitionCommand(
            entityName: (string) ($data['entity_name'] ?? ''),
            fieldName: (string) ($data['field_name'] ?? ''),
            fieldType: (string) ($data['field_type'] ?? 'string'),
            fieldScope: (string) ($data['field_scope'] ?? 'common'),
            sqlIndex: (string) ($data['sql_index'] ?? 'none'),
            titleWording: $this->nullableString($data['title_wording'] ?? null),
            titleDomain: $this->nullableString($data['title_domain'] ?? null),
            descriptionWording: $this->nullableString($data['description_wording'] ?? null),
            descriptionDomain: $this->nullableString($data['description_domain'] ?? null),
            displayFront: (bool) ($data['display_front'] ?? false),
            displayApi: (bool) ($data['display_api'] ?? false),
            displayBo: (bool) ($data['display_bo'] ?? true),
            displayGrid: (bool) ($data['display_grid'] ?? false),
            gridPosition: isset($data['grid_position']) && '' !== $data['grid_position'] ? (int) $data['grid_position'] : null,
            validator: $this->nullableString($data['validator'] ?? null),
            symfonyFieldType: $this->nullableString($data['symfony_field_type'] ?? null),
        );

        return (int) $this->commandBus->handle($command);
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data): void
    {
        $command = (new EditExtraPropertyDefinitionCommand((int) $id))
            ->setTitleWording($this->nullableString($data['title_wording'] ?? null))
            ->setTitleDomain($this->nullableString($data['title_domain'] ?? null))
            ->setDescriptionWording($this->nullableString($data['description_wording'] ?? null))
            ->setDescriptionDomain($this->nullableString($data['description_domain'] ?? null))
            ->setDisplayFront((bool) ($data['display_front'] ?? false))
            ->setDisplayApi((bool) ($data['display_api'] ?? false))
            ->setDisplayBo((bool) ($data['display_bo'] ?? true))
            ->setDisplayGrid((bool) ($data['display_grid'] ?? false))
            ->setGridPosition(isset($data['grid_position']) && '' !== $data['grid_position'] ? (int) $data['grid_position'] : null)
            ->setValidator($this->nullableString($data['validator'] ?? null))
            ->setSymfonyFieldType($this->nullableString($data['symfony_field_type'] ?? null));

        $this->commandBus->handle($command);
    }

    /**
     * Converts an empty string to null; returns the original string otherwise.
     *
     * @param mixed $value
     *
     * @return string|null
     */
    private function nullableString(mixed $value): ?string
    {
        if (null === $value || '' === $value) {
            return null;
        }

        return (string) $value;
    }
}
