<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Query\GetExtraPropertyDefinitionForEditing;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\QueryResult\ExtraPropertyDefinitionInfo;

/**
 * Provides form data for the ExtraFieldType when editing an existing core extra property definition.
 *
 * On create, getDefaultData() returns null so the form starts empty.
 * On edit, getData($id) fetches the definition row via CQRS and maps it to the form array.
 */
final class ExtraFieldFormDataProvider implements FormDataProviderInterface
{
    public function __construct(private readonly CommandBusInterface $queryBus)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @param int $id Definition primary key
     */
    public function getData($id): array
    {
        /** @var ExtraPropertyDefinitionInfo $info */
        $info = $this->queryBus->handle(new GetExtraPropertyDefinitionForEditing((int) $id));

        return [
            'id' => $info->getId(),
            // Structural fields (read-only reference, not submitted as edit inputs).
            'entity_name' => $info->getEntityName(),
            'field_name' => $info->getFieldName(),
            'field_type' => $info->getFieldType(),
            'field_scope' => $info->getFieldScope(),
            // Editable metadata.
            'title_wording' => $info->getTitleWording() ?? '',
            'title_domain' => $info->getTitleDomain() ?? '',
            'description_wording' => $info->getDescriptionWording() ?? '',
            'description_domain' => $info->getDescriptionDomain() ?? '',
            'display_front' => $info->isDisplayFront(),
            'display_api' => $info->isDisplayApi(),
            'display_bo' => $info->isDisplayBo(),
            'display_grid' => $info->isDisplayGrid(),
            'grid_position' => $info->getGridPosition(),
            'validator' => $info->getValidator() ?? '',
            'symfony_field_type' => $info->getSymfonyFieldType() ?? '',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData(): ?array
    {
        return [
            'field_scope' => 'common',
            'sql_index' => 'none',
            'display_bo' => true,
            'display_front' => false,
            'display_api' => false,
            'display_grid' => false,
        ];
    }
}
