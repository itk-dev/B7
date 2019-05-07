<?php

namespace App\Controller;

use AlterPHP\EasyAdminExtensionBundle\Controller\EasyAdminController as BaseAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AdminController.
 *
 * Controller for handling requests going to protected area
 */
class AdminController extends BaseAdminController
{
    /**
     * @return array
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), ['fos_user.user_manager' => UserManagerInterface::class]);
    }

    /**
     * Creates and returns a new instance of the User-class.
     *
     * @return \FOS\UserBundle\Model\UserInterface|mixed
     */
    public function createNewUserEntity()
    {
        return $this->get('fos_user.user_manager')->createUser();
    }

    /**
     * Persists an User entity in the database.
     *
     * @param User $user
     */
    public function persistUserEntity($user)
    {
        $this->get('fos_user.user_manager')->updateUser($user, false);
        parent::persistEntity($user);
    }

    /**
     * Updates an User entity.
     *
     * @param User $user
     */
    public function updateUserEntity($user)
    {
        $this->get('fos_user.user_manager')->updateUser($user, false);
        parent::updateEntity($user);
    }

    /**
     * Persists a Survey entity in the database.
     * If the User property in the Survey entity is not set, the currently logged in User will be
     * attached to the Entity.
     *
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
     * Action for listing Surveys.
     * If the currently logged in User has the admin role assigned, every Survey will be included in the response,
     * otherwise only the Surveys created by the currently logged in User will be included in the response.
     *
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
     * Custom action for showing a statistics page for a specific Survey.
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function statisticsAction(): Response
    {
        $responses = $this->getDoctrine()->getRepository(\App\Entity\Response::class);

        $surveyId = $this->request->query->get('id');

        $dateFormat = 'd/m/Y';

        $defaultFrom = (new \DateTime())->sub(new \DateInterval('P7D'));
        $defaultTo = new \DateTime();

        $defaultValues = [
            'from' => $defaultFrom->format($dateFormat),
            'to' => $defaultTo->format($dateFormat),
        ];
        $form = $this->createFormBuilder($defaultValues)
            ->add('from', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new DateTime(['format' => $dateFormat]),
                ],
            ])
            ->add('to', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new DateTime(['format' => $dateFormat]),
                ],
            ])
            ->getForm();

        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $answers = $responses->getAnswersBetweenDates(
                $surveyId,
                date_create_from_format($dateFormat, $formData['from']),
                date_create_from_format($dateFormat, $formData['to'])
            );

            $averageAnswers = $responses->getAnswersBetweenDates(
                $surveyId,
                new \DateTime(date($dateFormat, strtotime(0))),
                date_create_from_format($dateFormat, $formData['from'])
            );

            $avgAnswersWithLabels = $responses->getAverageAnswersOnDatesWithLabels($surveyId);

            return $this->render('statistics.html.twig', [
                'form' => $form->createView(),
                'answers' => $answers,
                'averageAnswers' => $averageAnswers,
                'allVotesLabels' => $avgAnswersWithLabels['labels'],
                'allVotesAverage' => $avgAnswersWithLabels['values'],
            ]);
        }

        $defaultPeriodAnswers = $responses->getAnswersBetweenDates($surveyId, $defaultFrom, $defaultTo);

        $averageAnswers = $responses->getAnswersBetweenDates(
            $surveyId,
            new \DateTime(date($dateFormat, strtotime(0))),
            $defaultFrom
        );

        $avgAnswersWithLabels = $responses->getAverageAnswersOnDatesWithLabels($surveyId);

        return $this->render('statistics.html.twig', [
            'form' => $form->createView(),
            'answers' => $defaultPeriodAnswers,
            'averageAnswers' => $averageAnswers,
            'allVotesLabels' => $avgAnswersWithLabels['labels'],
            'allVotesAverage' => $avgAnswersWithLabels['values'],
        ]);
    }

    /**
     * Redirects to a specific Survey based on a query parameter.
     * The query parameter 'id' has to be present.
     *
     * @return Response
     */
    public function redirectToSurveyAction(): Response
    {
        if (!$this->request->query->has('id')) {
            throw new Exception('Survey ID not present as query parameter.');
        }

        $surveyId = $this->request->query->get('id');

        if (empty($surveyId)) {
            throw new Exception('Survey ID not present as query parameter value.');
        }

        return $this->redirectToRoute('survey.show', ['surveyId' => $surveyId]);
    }

    /**
     * Creates an instance of Symfonys FormInterface based on an Entity and EasyAdmin configuration.
     * Fields wchich has the role property set in the EasyAdmin configuration will be checked against
     * the roles assigned to the currently logged in user, and if they don't match the fields will be removed
     * from the form.
     *
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
