<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\ExtraProperty\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Defines columns, filters, row actions and bulk actions for the Extra Fields management grid.
 *
 * The grid lists all rows from the extra_property_definition registry table and allows
 * deletion of any field and editing of core (_core / module_name = '') fields only.
 */
final class ExtraPropertyDefinitionGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use BulkDeleteActionTrait;
    use DeleteActionTrait;

    /** @var string Grid identifier used for filter persistence and route lookup */
    public const GRID_ID = 'extra_property_definition';

    /**
     * {@inheritdoc}
     */
    protected function getId(): string
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getName(): string
    {
        return $this->trans('Extra fields', [], 'Admin.Advparameters.Feature');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns(): ColumnCollection
    {
        return (new ColumnCollection())
            ->add(
                (new BulkActionColumn('bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_extra_property_definition',
                    ])
            )
            ->add(
                (new DataColumn('id_extra_property_definition'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_extra_property_definition',
                    ])
            )
            ->add(
                (new DataColumn('entity_name'))
                    ->setName($this->trans('Entity', [], 'Admin.Advparameters.Feature'))
                    ->setOptions([
                        'field' => 'entity_name',
                    ])
            )
            ->add(
                (new DataColumn('module_name'))
                    ->setName($this->trans('Module', [], 'Admin.Advparameters.Feature'))
                    ->setOptions([
                        'field' => 'module_name',
                    ])
            )
            ->add(
                (new DataColumn('field_name'))
                    ->setName($this->trans('Field name', [], 'Admin.Advparameters.Feature'))
                    ->setOptions([
                        'field' => 'field_name',
                    ])
            )
            ->add(
                (new DataColumn('storage_column_name'))
                    ->setName($this->trans('Storage column', [], 'Admin.Advparameters.Feature'))
                    ->setOptions([
                        'field' => 'storage_column_name',
                    ])
            )
            ->add(
                (new DataColumn('field_type'))
                    ->setName($this->trans('Type', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'field_type',
                    ])
            )
            ->add(
                (new DataColumn('field_scope'))
                    ->setName($this->trans('Scope', [], 'Admin.Advparameters.Feature'))
                    ->setOptions([
                        'field' => 'field_scope',
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add(
                                (new LinkRowAction('edit'))
                                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                                    ->setIcon('edit')
                                    ->setOptions([
                                        'route' => 'admin_extra_fields_edit',
                                        'route_param_name' => 'extraFieldId',
                                        'route_param_field' => 'id_extra_property_definition',
                                        'clickable_row' => true,
                                    ])
                            )
                            ->add(
                                $this->buildDeleteAction(
                                    'admin_extra_fields_delete',
                                    'extraFieldId',
                                    'id_extra_property_definition',
                                    Request::METHOD_DELETE,
                                )
                            ),
                    ])
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): FilterCollection
    {
        // Build ENUM choice arrays from the enum definitions.
        $typeChoices = array_combine(ExtraPropertyType::values(), ExtraPropertyType::values());
        $scopeChoices = array_combine(ExtraPropertyScope::values(), ExtraPropertyScope::values());

        return (new FilterCollection())
            ->add(
                (new Filter('id_extra_property_definition', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => ['placeholder' => $this->trans('ID', [], 'Admin.Global')],
                    ])
                    ->setAssociatedColumn('id_extra_property_definition')
            )
            ->add(
                (new Filter('entity_name', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => ['placeholder' => $this->trans('Entity', [], 'Admin.Advparameters.Feature')],
                    ])
                    ->setAssociatedColumn('entity_name')
            )
            ->add(
                (new Filter('module_name', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => ['placeholder' => $this->trans('Module', [], 'Admin.Advparameters.Feature')],
                    ])
                    ->setAssociatedColumn('module_name')
            )
            ->add(
                (new Filter('field_name', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => ['placeholder' => $this->trans('Field name', [], 'Admin.Advparameters.Feature')],
                    ])
                    ->setAssociatedColumn('field_name')
            )
            ->add(
                (new Filter('field_type', ChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'choices' => $typeChoices,
                        'choice_translation_domain' => false,
                        'placeholder' => $this->trans('All types', [], 'Admin.Advparameters.Feature'),
                    ])
                    ->setAssociatedColumn('field_type')
            )
            ->add(
                (new Filter('field_scope', ChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'choices' => $scopeChoices,
                        'choice_translation_domain' => false,
                        'placeholder' => $this->trans('All scopes', [], 'Admin.Advparameters.Feature'),
                    ])
                    ->setAssociatedColumn('field_scope')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'admin_extra_fields_index',
                    ])
                    ->setAssociatedColumn('actions')
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions(): BulkActionCollection
    {
        return (new BulkActionCollection())
            ->add(
                $this->buildBulkDeleteAction('admin_extra_fields_delete_bulk')
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions(): GridActionCollection
    {
        return (new GridActionCollection())
            ->add(
                (new SimpleGridAction('common_refresh_list'))
                    ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
                    ->setIcon('refresh')
            )
            ->add(
                (new SimpleGridAction('common_show_query'))
                    ->setName($this->trans('Show SQL query', [], 'Admin.Actions'))
                    ->setIcon('code')
            )
            ->add(
                (new SimpleGridAction('common_export_sql_manager'))
                    ->setName($this->trans('Export to SQL Manager', [], 'Admin.Actions'))
                    ->setIcon('storage')
            );
    }
}
