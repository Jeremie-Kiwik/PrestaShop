<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\ExtraField;

use PrestaShop\PrestaShop\Core\ExtraProperty\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\ExtraPropertyType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Symfony form type for creating or editing a core extra property definition.
 *
 * In create mode (is_edit = false) structural fields are included:
 *   entity_name, field_name, field_type, field_scope, sql_index.
 *
 * In edit mode (is_edit = true) only metadata fields are editable (display labels,
 * visibility flags, advanced options). Structural fields are intentionally excluded
 * because changing them would require DDL alterations.
 */
class ExtraFieldType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = (bool) $options['is_edit'];
        // When read_only is true, all inputs get the HTML disabled attribute.
        // This is used for module-managed fields that cannot be edited through the BO.
        $disabled = (bool) $options['read_only'];
        $disabledAttr = $disabled ? ['disabled' => 'disabled'] : [];

        // ── Structural fields (create only) ────────────────────────────────────
        if (!$isEdit) {
            $typeChoices = array_combine(ExtraPropertyType::values(), ExtraPropertyType::values());
            $scopeChoices = array_combine(ExtraPropertyScope::values(), ExtraPropertyScope::values());

            $builder
                ->add('entity_name', TextType::class, [
                    'label' => $this->trans('Entity name', 'Admin.Advparameters.Feature'),
                    'help' => $this->trans('The entity table name (e.g. "product", "customer").', 'Admin.Advparameters.Help'),
                    'constraints' => [
                        new NotBlank([
                            'message' => $this->trans('The %s field is required.', 'Admin.Notifications.Error', ['"Entity name"']),
                        ]),
                        new Regex([
                            'pattern' => '/^[a-z][a-z0-9_]{0,63}$/',
                            'message' => $this->trans(
                                'Only lowercase letters, digits and underscores are allowed (max 64 characters).',
                                'Admin.Notifications.Error',
                            ),
                        ]),
                    ],
                ])
                ->add('field_name', TextType::class, [
                    'label' => $this->trans('Field name', 'Admin.Advparameters.Feature'),
                    'help' => $this->trans('Logical identifier for the field (e.g. "custom_ref"). Only lowercase letters, digits and underscores.', 'Admin.Advparameters.Help'),
                    'constraints' => [
                        new NotBlank([
                            'message' => $this->trans('The %s field is required.', 'Admin.Notifications.Error', ['"Field name"']),
                        ]),
                        new Regex([
                            'pattern' => '/^[a-z][a-z0-9_]{0,63}$/',
                            'message' => $this->trans(
                                'Only lowercase letters, digits and underscores are allowed (max 64 characters).',
                                'Admin.Notifications.Error',
                            ),
                        ]),
                    ],
                ])
                ->add('field_type', ChoiceType::class, [
                    'label' => $this->trans('Field type', 'Admin.Advparameters.Feature'),
                    'choices' => $typeChoices,
                    'choice_translation_domain' => false,
                    'constraints' => [
                        new NotBlank([
                            'message' => $this->trans('The %s field is required.', 'Admin.Notifications.Error', ['"Field type"']),
                        ]),
                    ],
                ])
                ->add('field_scope', ChoiceType::class, [
                    'label' => $this->trans('Scope', 'Admin.Advparameters.Feature'),
                    'help' => $this->trans('"common" = one value per entity, "lang" = per language, "shop" = per shop.', 'Admin.Advparameters.Help'),
                    'choices' => $scopeChoices,
                    'choice_translation_domain' => false,
                    'data' => 'common',
                ])
                ->add('sql_index', ChoiceType::class, [
                    'label' => $this->trans('SQL index', 'Admin.Advparameters.Feature'),
                    'choices' => [
                        $this->trans('None', 'Admin.Global') => 'none',
                        $this->trans('Index (KEY)', 'Admin.Advparameters.Feature') => 'key',
                        $this->trans('Unique index', 'Admin.Advparameters.Feature') => 'unique',
                    ],
                    'data' => 'none',
                ]);
        }

        // ── Display labels ──────────────────────────────────────────────────────
        $builder
            ->add('title_wording', TextType::class, [
                'label' => $this->trans('Label text', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Display label shown in BO forms and grids.', 'Admin.Advparameters.Help'),
                'required' => false,
                'attr' => $disabledAttr,
                'constraints' => [
                    new Length(['max' => 255]),
                ],
            ])
            ->add('title_domain', TextType::class, [
                'label' => $this->trans('Label translation domain', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Symfony translation domain for the label (e.g. "Admin.Global"). Leave empty to use the wording as-is.', 'Admin.Advparameters.Help'),
                'required' => false,
                'data' => 'Admin.Global',
                'attr' => $disabledAttr,
                'constraints' => [
                    new Length(['max' => 255]),
                ],
            ])
            ->add('description_wording', TextType::class, [
                'label' => $this->trans('Help text', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Short description shown below the field in BO forms.', 'Admin.Advparameters.Help'),
                'required' => false,
                'attr' => $disabledAttr,
                'constraints' => [
                    new Length(['max' => 255]),
                ],
            ])
            ->add('description_domain', TextType::class, [
                'label' => $this->trans('Help text translation domain', 'Admin.Advparameters.Feature'),
                'required' => false,
                'data' => 'Admin.Global',
                'attr' => $disabledAttr,
                'constraints' => [
                    new Length(['max' => 255]),
                ],
            ]);

        // ── Visibility flags ────────────────────────────────────────────────────
        $builder
            ->add('display_bo', SwitchType::class, [
                'label' => $this->trans('Show in Back-Office forms', 'Admin.Advparameters.Feature'),
                'required' => false,
                'attr' => $disabledAttr,
            ])
            ->add('display_grid', SwitchType::class, [
                'label' => $this->trans('Show in Back-Office grids', 'Admin.Advparameters.Feature'),
                'required' => false,
                'attr' => $disabledAttr,
            ])
            ->add('display_front', SwitchType::class, [
                'label' => $this->trans('Expose to front-office templates', 'Admin.Advparameters.Feature'),
                'required' => false,
                'attr' => $disabledAttr,
            ])
            ->add('display_api', SwitchType::class, [
                'label' => $this->trans('Expose via Admin API', 'Admin.Advparameters.Feature'),
                'required' => false,
                'attr' => $disabledAttr,
            ])
            ->add('grid_position', IntegerType::class, [
                'label' => $this->trans('Grid column position', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Numeric position of the column in BO grids. Leave empty for automatic ordering.', 'Admin.Advparameters.Help'),
                'required' => false,
                'attr' => $disabledAttr,
            ]);

        // ── Advanced ────────────────────────────────────────────────────────────
        $builder
            ->add('validator', TextType::class, [
                'label' => $this->trans('Validator', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Optional Validate:: method name used to validate the value (e.g. "isEmail").', 'Admin.Advparameters.Help'),
                'required' => false,
                'attr' => $disabledAttr,
                'constraints' => [
                    new Length(['max' => 64]),
                ],
            ])
            ->add('symfony_field_type', TextType::class, [
                'label' => $this->trans('Symfony field type', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Fully-qualified Symfony form type class to override the default widget (e.g. "Symfony\\Component\\Form\\Extension\\Core\\Type\\EmailType").', 'Admin.Advparameters.Help'),
                'required' => false,
                'attr' => $disabledAttr,
                'constraints' => [
                    new Length(['max' => 255]),
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // When true, structural fields (entity_name, field_name, field_type, field_scope, sql_index)
            // are not rendered — they cannot be changed without DDL alterations.
            'is_edit' => false,
            // When true, the form is rendered in read-only mode (module-managed fields).
            // All inputs are disabled so the user can inspect values but not submit changes.
            'read_only' => false,
        ]);
        $resolver->setAllowedTypes('is_edit', 'bool');
        $resolver->setAllowedTypes('read_only', 'bool');
    }
}
