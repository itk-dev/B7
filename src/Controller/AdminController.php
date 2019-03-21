<?php

namespace App\Controller;

use AlterPHP\EasyAdminExtensionBundle\Controller\EasyAdminController as BaseAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;

class AdminController extends BaseAdminController
{
    public function createNewUserEntity()
    {
        return $this->get("fos_user.user_manager")->createUser();
    }

    public function persistUserEntity($user)
    {
        $this->get("fos_user.user_manager")->updateUser($user, false);
        parent::persistEntity($user);
    }

    public function updateUserEntity($user)
    {
        $this->get("fos_user.user_manager")->updateUser($user, false);
        parent::updateEntity($user);
    }

    public function createSurveyNewForm($entity, $fields)
    {
        $form = parent::createNewForm($entity, $fields);

        // We remove fields from the form if the currently logged in
        // user is not allowed to set a value for a specific field.
        foreach ($fields as $name => $field)
        {
            if ( empty($field['role']) )
            {
                continue;
            }

            if ( ! $this->isGranted($field['role']) )
            {
                $form->remove($name);
            }
        }

        return $form;
    }

    public function persistSurveyEntity($entity)
    {
        // Making sure there always is a user attached to the Survey.
        // If the currently logged in User is not an admin, no User will be attached before now
        // due to the User field in the form for creating a Survey is only showed to admins.
        if ( empty($entity->getUser()) )
        {
            $entity->setUser($this->getUser());
        }

        parent::persistEntity($entity);
    }
}