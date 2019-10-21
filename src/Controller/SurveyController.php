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
    private $logger;
    private $surveys;
    private $responses;

    /**
     * SurveyController constructor.
     *
     * @param LoggerInterface    $logger
     * @param SurveyRepository   $surveys
     * @param ResponseRepository $responses
     */
    public function __construct(LoggerInterface $logger, SurveyRepository $surveys, ResponseRepository $responses)
    {
        $this->logger = $logger;
        $this->surveys = $surveys;
        $this->responses = $responses;
    }

    /**
     * Displays a specific Survey.
     *
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
     * Handles a reply to a specific Survey.
     * It expects to the smiley, what and datetime keys to be present in request data.
     *
     * @param int     $surveyId
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function reply(int $surveyId, Request $request): Response
    {
        $survey = $this->surveys->find($surveyId);

        if (empty($survey)) {
            $this->logger->critical('Replying to survey with id '.$surveyId.' which does not exist!');
            throw new HttpException(500);
        }

        $answer = $request->get('smiley');
        $followUpAnswer = $request->get('what');
        $createdAt = new \DateTime(strtotime($request->get('datetime')));

        try {
            $response = new \App\Entity\Response($survey, $answer, $followUpAnswer, $createdAt);
            $this->responses->add($response);
        } catch (\InvalidArgumentException $ie) {
            $this->logger->critical($ie->getMessage());
            throw new HttpException(422); // Validation failed.
        } catch (ORMException $oe) {
            $this->logger->critical($oe->getMessage());
            throw new HttpException(500);
        }

        return new JsonResponse(['result' => 'ok']);
    }

    public function explanation(Request $request): Response
    {
        return $this->render('survey_explanation.html.twig');
    }
}
