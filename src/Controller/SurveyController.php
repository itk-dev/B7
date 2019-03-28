<?php

namespace App\Controller;

use App\Repository\ResponseRepository;
use App\Repository\SurveyRepository;
use Doctrine\ORM\ORMException;
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
    private $responses;

    /**
     * SurveyController constructor.
     *
     * @param SurveyRepository   $surveys
     * @param ResponseRepository $responses
     */
    public function __construct(SurveyRepository $surveys, ResponseRepository $responses)
    {
        $this->surveys = $surveys;
        $this->responses = $responses;
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
     * @param int             $surveyId
     * @param Request         $request
     * @param LoggerInterface $logger
     *
     * @return Response
     *
     * @throws \Exception
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

        try {
            $this->responses->add($response);
        } catch (ORMException $oe) {
            $logger->critical($oe->getMessage());
            throw new HttpException(500);
        }

        return new JsonResponse(['result' => 'ok']);
    }
}