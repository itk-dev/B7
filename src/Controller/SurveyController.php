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

        try {
            $response = new \App\Entity\Response($survey, $answer, $followUpAnswer, $createdAt);
            $this->responses->add($response);
        } catch (\InvalidArgumentException $ie) {
            $logger->critical($ie->getMessage());
            throw new HttpException(422); // Validation failed.
        } catch (ORMException $oe) {
            $logger->critical($oe->getMessage());
            throw new HttpException(500);
        }

        return new JsonResponse(['result' => 'ok']);
    }
}