<?php

/**
 * @license MIT
 * @license https://opensource.org/licenses/MIT The MIT License
 */

namespace App\Controller;

use AlterPHP\EasyAdminExtensionBundle\Controller\EasyAdminController as BaseAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;

/**
 * Class AdminController.
 *
 * Controller for handling requests going to protected area
 */
class AdminController extends BaseAdminController
{
    /**
     * @return \FOS\UserBundle\Model\UserInterface|mixed
     */
    public function createNewUserEntity()
    {
        return $this->get('fos_user.user_manager')->createUser();
    }

    /**
     * @param User $user
     */
    public function persistUserEntity($user)
    {
        $this->get('fos_user.user_manager')->updateUser($user, false);
        parent::persistEntity($user);
    }

    /**
     * @param User $user
     */
    public function updateUserEntity($user)
    {
        $this->get('fos_user.user_manager')->updateUser($user, false);
        parent::updateEntity($user);
    }

    /**
     * @param object $entity
     */
    public function persistSurveyEntity($entity)
    {
        // Making sure there always is a user attached to the Survey.
        // If the currently logged in User is not an admin, no User will be attached before now
        // due to the User field in the form for creating a Survey is only showed to admins.
        if (empty($entity->getUser())) {
            $entity->setUser($this->getUser());
        }

        parent::persistEntity($entity);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listSurveyAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_LIST);

        $user = $this->getUser();

        // We only want to show Surveys for the currently logged in user
        // except if the currently logged in user has the admin role.
        if (!$this->isGranted('ROLE_ADMIN')) {
            $currentDqlFilter = $this->entity['list']['dql_filter'];

            $currentUserDqlFilter = 'entity.user = '.$user->getId();

            $newDqlFilter = $this->appendDqlFilterToDqlFilter($currentDqlFilter, $currentUserDqlFilter);

            $this->entity['list']['dql_filter'] = $newDqlFilter;
        }

        return $this->listAction();
    }

    /**
     * Generic listAction which now filters fields shown based on currently logged in user.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_LIST);

        $fields = $this->entity['list']['fields'];

        $this->entity['list']['fields'] = $this->getFilteredListOfFieldsOnRole($fields);

        return parent::listAction();
    }

    /**
     * @param object $entity
     * @param array  $fields
     * @param string $view
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @throws \Exception
     */
    protected function createEntityForm($entity, $fields, $view)
    {
        $form = parent::createEntityForm($entity, $fields, $view);

        // We remove fields from the form if the currently logged in
        // user is not allowed to set a value for a specific field.
        foreach ($fields as $name => $field) {
            if (empty($field['role'])) {
                continue;
            }

            if (!$this->isGranted($field['role'])) {
                $form->remove($name);
            }
        }

        return $form;
    }

    /**
     * Filters the provided list of fields on role if set in config file.
     *
     * @param array $fields Fields needed to be filtered
     *
     * @return array Filtered list of fields
     */
    private function getFilteredListOfFieldsOnRole(array $fields): array
    {
        return array_filter($fields, function ($field) {
            if (!empty($field['role'])) {
                return ($this->isGranted($field['role'])) ? $field : null;
            }

            return $field;
        });
    }

    /**
     * Appends new dql filter to an existing dql filter.
     * If existing dql filter is empty the new dql filter will be returned.
     *
     * @param string $dqlFilter    Dql filter that will have a new filter appended to
     * @param string $newDqlFilter Dql filter that will be appended
     *
     * @return string
     */
    private function appendDqlFilterToDqlFilter($dqlFilter, $newDqlFilter)
    {
        if (empty($dqlFilter)) {
            return $newDqlFilter;
        }

        $dqlFilter .= 'AND '.$newDqlFilter;

        return $dqlFilter;
    }
}
