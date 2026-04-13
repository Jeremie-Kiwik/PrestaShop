<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\DeleteExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ExtraPropertyDomainException;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Query\GetExtraPropertyDefinitionForEditing;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\QueryResult\ExtraPropertyDefinitionInfo;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\ExtraFieldFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manages the "Configure > Advanced Parameters > Extra Fields" page.
 *
 * Lists all registered extra property definitions and provides create/edit/delete actions.
 * Only '_core' fields (module_name = '') may be edited or created through this controller.
 * Module-managed fields can only be deleted from here.
 */
class ExtraFieldController extends PrestaShopAdminController
{
    /**
     * Displays the Extra Fields list grid.
     *
     * @param Request $request
     * @param ExtraFieldFilters $filters
     * @param GridFactoryInterface $gridFactory
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        ExtraFieldFilters $filters,
        #[Autowire(service: 'prestashop.core.grid.factory.extra_field')]
        GridFactoryInterface $gridFactory,
    ): Response {
        $grid = $gridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/ExtraField/index.html.twig', [
            'layoutHeaderToolbarBtn' => [
                'add' => [
                    'href' => $this->generateUrl('admin_extra_fields_create'),
                    'desc' => $this->trans('Add new extra field', [], 'Admin.Advparameters.Feature'),
                    'icon' => 'add_circle_outline',
                ],
            ],
            'layoutTitle' => $this->trans('Extra fields', [], 'Admin.Advparameters.Feature'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'extraFieldGrid' => $this->presentGrid($grid),
        ]);
    }

    /**
     * Shows the create form and handles its submission.
     *
     * The new field is always a '_core' field (empty module_name).
     *
     * @param Request $request
     * @param FormBuilderInterface $formBuilder
     * @param FormHandlerInterface $formHandler
     *
     * @return Response|RedirectResponse
     */
    #[AdminSecurity(
        "is_granted('create', request.get('_legacy_controller'))",
        message: 'You do not have permission to create this.',
        redirectRoute: 'admin_extra_fields_index',
    )]
    public function createAction(
        Request $request,
        #[Autowire(service: 'prestashop.core.form.builder.extra_field_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.extra_field_form_handler')]
        FormHandlerInterface $formHandler,
    ): Response|RedirectResponse {
        $extraFieldForm = $formBuilder->getForm([], ['is_edit' => false]);
        $extraFieldForm->handleRequest($request);

        try {
            $result = $formHandler->handle($extraFieldForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_extra_fields_index');
            }
        } catch (ExtraPropertyDomainException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e));
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/ExtraField/create.html.twig', [
            'layoutTitle' => $this->trans('New extra field', [], 'Admin.Advparameters.Feature'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'extraFieldForm' => $extraFieldForm->createView(),
        ]);
    }

    /**
     * Shows the edit form and handles its submission.
     *
     * Only '_core' fields (module_name = '') may be edited. Module-managed fields are read-only.
     *
     * @param int $extraFieldId Definition primary key
     * @param Request $request
     * @param FormBuilderInterface $formBuilder
     * @param FormHandlerInterface $formHandler
     *
     * @return Response|RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_extra_fields_index')]
    #[AdminSecurity(
        "is_granted('update', request.get('_legacy_controller'))",
        message: 'You do not have permission to edit this.',
        redirectRoute: 'admin_extra_fields_index',
    )]
    public function editAction(
        int $extraFieldId,
        Request $request,
        #[Autowire(service: 'prestashop.core.form.builder.extra_field_form_builder')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.core.form.identifiable_object.extra_field_form_handler')]
        FormHandlerInterface $formHandler,
    ): Response|RedirectResponse {
        // Verify the definition exists and is a core field before showing the form.
        try {
            /** @var ExtraPropertyDefinitionInfo $info */
            $info = $this->dispatchQuery(new GetExtraPropertyDefinitionForEditing($extraFieldId));
        } catch (ExtraPropertyDomainException) {
            $this->addFlash('error', $this->trans('The object cannot be loaded (or found).', [], 'Admin.Notifications.Error'));

            return $this->redirectToRoute('admin_extra_fields_index');
        }

        // Module-managed fields are shown in read-only mode: the user can inspect all values
        // but the form inputs are disabled and the save button is hidden.
        $isCoreField = '' === $info->getModuleName();

        $extraFieldForm = $formBuilder->getFormFor($extraFieldId, [], [
            'is_edit' => true,
            'read_only' => !$isCoreField,
        ]);
        $extraFieldForm->handleRequest($request);

        // Only process the submission for core fields; module fields are read-only.
        if ($isCoreField) {
            try {
                $result = $formHandler->handleFor($extraFieldId, $extraFieldForm);

                if ($result->isSubmitted() && $result->isValid()) {
                    $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));

                    return $this->redirectToRoute('admin_extra_fields_index');
                }
            } catch (ExtraPropertyDomainException $e) {
                $this->addFlash('error', $this->getErrorMessageForException($e));
            }
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/ExtraField/edit.html.twig', [
            'layoutTitle' => $this->trans('Editing extra field "%name%"', ['%name%' => $info->getFieldName()], 'Admin.Advparameters.Feature'),
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'extraFieldForm' => $extraFieldForm->createView(),
            'extraFieldInfo' => $info,
            'isCoreField' => $isCoreField,
        ]);
    }

    /**
     * Deletes a single extra property definition (drops the physical column by default).
     *
     * Any field (core or module-managed) may be deleted from the BO. Module owners are
     * responsible for re-registering their fields on next install/upgrade if needed.
     *
     * @param int $extraFieldId Definition primary key
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_extra_fields_index')]
    #[AdminSecurity(
        "is_granted('delete', request.get('_legacy_controller'))",
        message: 'You do not have permission to delete this.',
        redirectRoute: 'admin_extra_fields_index',
    )]
    public function deleteAction(int $extraFieldId): RedirectResponse
    {
        try {
            $this->dispatchCommand(new DeleteExtraPropertyDefinitionCommand($extraFieldId, dropColumn: true));

            $this->addFlash('success', $this->trans('Successful deletion', [], 'Admin.Notifications.Success'));
        } catch (ExtraPropertyDomainException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e));
        }

        return $this->redirectToRoute('admin_extra_fields_index');
    }

    /**
     * Processes a bulk-delete of extra property definitions.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_extra_fields_index')]
    #[AdminSecurity(
        "is_granted('delete', request.get('_legacy_controller'))",
        message: 'You do not have permission to delete this.',
        redirectRoute: 'admin_extra_fields_index',
    )]
    public function deleteBulkAction(Request $request): RedirectResponse
    {
        $ids = array_map('intval', $request->request->all('extra_property_definition_bulk'));

        $errors = [];
        foreach ($ids as $id) {
            try {
                $this->dispatchCommand(new DeleteExtraPropertyDefinitionCommand($id, dropColumn: true));
            } catch (ExtraPropertyDomainException $e) {
                $errors[] = $this->getErrorMessageForException($e);
            }
        }

        if (!empty($errors)) {
            $this->addFlashErrors($errors);
        } else {
            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', [], 'Admin.Notifications.Success'),
            );
        }

        return $this->redirectToRoute('admin_extra_fields_index');
    }
}
