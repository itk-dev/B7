<?php

namespace App\Controller;

use App\Repository\SurveyRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
    public function reply(int $surveyId, Request $request, LoggerInterface $logger): Response
    {
        $survey = $this->surveys->find($surveyId);

        if (empty($survey)) {
            $logger->critical('Replying to survey with id '.$surveyId.' which does not exist!');
            throw new HttpException(500);
        }

        $answer = $request->get('smiley');
        $followUpAnswer = $request->get('what');
        $createdAt = new \DateTime(\strtotime($request->get('datetime')));

        $response = new \App\Entity\Response();

        $response->setAnswer($answer);
        $response->setFollowUpAnswer($followUpAnswer);
        $response->setCreatedAt($createdAt);

        $survey->addResponse($response);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($response);

        $entityManager->flush();

        return new JsonResponse(['result' => 'ok']);
        //return $this->redirectToRoute('survey.show', ['surveyId' => $surveyId]);
    }
}