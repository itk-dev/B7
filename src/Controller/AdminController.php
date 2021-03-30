<?php

namespace App\Controller;

use AlterPHP\EasyAdminExtensionBundle\Controller\EasyAdminController as BaseAdminController;
use App\Entity\Survey;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use DoctrineBatchUtils\BatchProcessing\SimpleBatchIteratorAggregate;
use DoctrineExtensions\Query\Mysql\Date;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Entity\Response as SurveyResponse;

/**
 * Class AdminController.
 *
 * Controller for handling requests going to protected area
 */
class AdminController extends BaseAdminController
{
    /**
     * Exports the responses of a given survey.
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     */
    public function exportResponsesAction()
    {
        $surveyId = $this->request->query->get('id');
        $entityManager = $this->em;

        /** @var Survey $survey */
        $survey = $entityManager->getRepository(Survey::class)->find($surveyId);

        $smileys = ['Meget utilfreds', 'Utilfreds', 'Mellem', 'Glad', 'Meget glad'];
        $followUpQuestions = [
            $survey->getNegativeFollowUp(),
            $survey->getNeutralFollowUp(),
            $survey->getPositiveFollowUp(),
        ];
        $followUpAnswers = [
            $survey->getFollowUpText1(),
            $survey->getFollowUpText2(),
            $survey->getFollowUpText3(),
            $survey->getFollowUpText4(),
            $survey->getFollowUpText5(),
        ];

        $query = $entityManager->getRepository(SurveyResponse::class)
            ->createQueryBuilder('r')
            ->where('r.survey = :surveyId')
            ->setParameter('surveyId', $surveyId)
            ->getQuery();

        $iterableSurveyRecords = SimpleBatchIteratorAggregate::fromQuery(
            $query,
            100
        );

        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile('php://output');

        $response = new StreamedResponse();

        $response->setCallback(function () use ($iterableSurveyRecords, $writer, $survey, $smileys, $followUpAnswers, $followUpQuestions) {
            $boldStyle = (new StyleBuilder())
                ->setFontBold()
                ->build();

            $rows = [
                WriterEntityFactory::createRow([
                    WriterEntityFactory::createCell('Titel', $boldStyle),
                    WriterEntityFactory::createCell($survey->getTitle()),
                ]),
                WriterEntityFactory::createRow([
                    WriterEntityFactory::createCell('Eksportdato', $boldStyle),
                    WriterEntityFactory::createCell((new \DateTime())->format('c')),
                ]),
                WriterEntityFactory::createRow([
                    WriterEntityFactory::createCell('Spørgsmål', $boldStyle),
                    WriterEntityFactory::createCell($survey->getQuestion()),
                ]),
                WriterEntityFactory::createRow([
                    WriterEntityFactory::createCell('Positiv opfølgning', $boldStyle),
                    WriterEntityFactory::createCell($survey->getPositiveFollowUp()),
                ]),
                WriterEntityFactory::createRow([
                    WriterEntityFactory::createCell('Neutral opfølgning', $boldStyle),
                    WriterEntityFactory::createCell($survey->getNeutralFollowUp()),
                ]),
                WriterEntityFactory::createRow([
                    WriterEntityFactory::createCell('Negativ opfølgning', $boldStyle),
                    WriterEntityFactory::createCell($survey->getNegativeFollowUp()),
                ]),
                WriterEntityFactory::createRow([
                    WriterEntityFactory::createCell('1. svarmulighed', $boldStyle),
                    WriterEntityFactory::createCell($survey->getFollowUpText1()),
                ]),
                WriterEntityFactory::createRow([
                    WriterEntityFactory::createCell('2. svarmulighed', $boldStyle),
                    WriterEntityFactory::createCell($survey->getFollowUpText2()),
                ]),
                WriterEntityFactory::createRow([
                    WriterEntityFactory::createCell('3. svarmulighed', $boldStyle),
                    WriterEntityFactory::createCell($survey->getFollowUpText3()),
                ]),
                WriterEntityFactory::createRow([
                    WriterEntityFactory::createCell('4. svarmulighed', $boldStyle),
                    WriterEntityFactory::createCell($survey->getFollowUpText4()),
                ]),
                WriterEntityFactory::createRow([
                    WriterEntityFactory::createCell('5. svarmulighed', $boldStyle),
                    WriterEntityFactory::createCell($survey->getFollowUpText5()),
                ]),
                WriterEntityFactory::createRowFromArray([]),
                WriterEntityFactory::createRowFromArray([]),
            ];
            $writer->addRows($rows);

            $row = WriterEntityFactory::createRowFromArray([
                'ID',
                'Tidspunkt',
                'Tilfredshed',
                'Opfølgende spørgsmål',
                'Opfølgende svar',
                'Svar (numerisk værdi)',
                'Opfølgende svar (numerisk værdi)',
            ], $boldStyle);
            $writer->addRow($row);

            /** @var array $surveyResponseArray */
            foreach ($iterableSurveyRecords as $surveyResponseArray) {
                /** @var SurveyResponse $surveyResponse */
                $surveyResponse = $surveyResponseArray[0];

                $answerIndex = $surveyResponse->getAnswer() - 1;

                if ($answerIndex == 0 || $answerIndex == 1) {
                    $followUpQuestionIndex = 0;
                } else if ($answerIndex == 2) {
                    $followUpQuestionIndex = 1;
                } else {
                    $followUpQuestionIndex = 2;
                }

                $row = WriterEntityFactory::createRowFromArray([
                    $surveyResponse->getId(),
                    $surveyResponse->getCreatedAt()->format('c'),
                    $smileys[$answerIndex],
                    $followUpQuestions[$followUpQuestionIndex],
                    $followUpAnswers[$surveyResponse->getFollowUpAnswer() - 1],
                    $surveyResponse->getAnswer(),
                    $surveyResponse->getFollowUpAnswer()
                ]);
                $writer->addRow($row);
            }

            $writer->close();
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="result.xlsx"');
        $response->setStatusCode(Response::HTTP_OK);

        return $response;
    }

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
     * @throws \HttpException
     */
    public function redirectToSurveyAction(): Response
    {
        if (!$this->request->query->has('id')) {
            throw new \HttpException('Survey ID not present as query parameter', 500);
        }

        $surveyId = $this->request->query->get('id');

        if (empty($surveyId)) {
            throw new \HttpException('Survey ID not present as query parameter value', 500);
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
