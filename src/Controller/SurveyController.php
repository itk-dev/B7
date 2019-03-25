<?php

namespace App\Controller;

use App\Repository\SurveyRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class SurveyController.
 */
class SurveyController extends AbstractController
{
    private $surveys;

    /**
     * SurveyController constructor.
     * @param SurveyRepository $surveys
     */
    public function __construct(SurveyRepository $surveys)
    {
        $this->surveys = $surveys;
    }

    /**
     * @param int $surveyId
     *
     * @return Response
     */
    public function show(int $surveyId): Response
    {
        $survey = $this->surveys->find($surveyId);

        if (empty($survey)) {
            throw $this->createNotFoundException('Could not find Survey.');
        }

        return $this->render('survey.html.twig', ['survey' => $survey]);
    }

    /**
     * @param int     $surveyId
     * @param Request $request
     *
     * @return Response
     */
    public function reply(int $surveyId, Request $request): Response
    {
        $survey = $this->surveys->find($surveyId);

        $response = new \App\Entity\Response();
        $response->setAnswer($request->get('answer'));
        $response->setFollowUpAnswer($request->get('followUpAnswer'));

        $survey->addResponse($response);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($response);

        $entityManager->flush();

        return $this->redirectToRoute('survey.show', ['surveyId' => $surveyId]);
    }
}