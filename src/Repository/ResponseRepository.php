<?php

namespace App\Repository;

use App\Entity\Response;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Response|null find($id, $lockMode = null, $lockVersion = null)
 * @method Response|null findOneBy(array $criteria, array $orderBy = null)
 * @method Response[]    findAll()
 * @method Response[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResponseRepository extends ServiceEntityRepository
{
    /**
     * ResponseRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Response::class);
    }

    /**
     * @param Response $response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function add(Response $response)
    {
        $entityManager = $this->getEntityManager();

        $entityManager->persist($response);

        $entityManager->flush();
    }

    /**
     * Returns a list of answer-percentages in a period sorted by answer group (1-5).
     *
     * @param int       $surveyId
     * @param \DateTime $fromDate
     * @param \DateTime $toDate
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getAnswersBetweenDates(int $surveyId, \DateTime $fromDate, \DateTime $toDate): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery('
            SELECT r.answer,
                   COUNT(r.answer) as answers
            FROM App\Entity\Response r
            WHERE r.survey = :surveyId
            AND r.createdAt BETWEEN :from AND :to
            GROUP BY r.answer
        ');

        $query->setParameter('surveyId', $surveyId);
        $query->setParameter('from', $fromDate->format('Y-m-d'));
        $query->setParameter('to', $toDate->add(new \DateInterval('P1D'))->format('Y-m-d'));

        $values = $query->getResult();

        $totalAnswers = 0;

        foreach ($values as $value) {
            $totalAnswers += $value['answers'];
        }

        // The code iterate through 1 to 5 as thay are the only available answer options.
        // Each time we check if there is a value available in the query result and if we
        // find one we calculate the percentage the number of times the answer option has
        // been selected holds of the count of all answers given.
        // If no value is found we add 0 as the result of the calculation.
        $newValues = [];

        for ($i = 1; $i < 6; ++$i) {
            $answers = 0;
            foreach ($values as $value) {
                if ($i === $value['answer']) {
                    $answers = floor((int) $value['answers'] / $totalAnswers * 100);
                }
            }

            $newValues[] = $answers;
        }

        return $newValues;
    }

    /**
     * Returns the average of answers for a Survey sorted by date in ascending order.
     * The array returned has to keys, labels which contains an array with the dates that have answers,
     * and values which contains an array with the average of answers on a date. The first entry in the values
     * array is the average answers for the first entry in the labels array.
     *
     * @param int $surveyId
     *
     * @return array
     */
    public function getAverageAnswersOnDatesWithLabels(int $surveyId): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery('
            SELECT r.answer,
                   COUNT(r.answer) as answers,
                   DATE(r.createdAt) as dateCreated
            FROM App\Entity\Response r
            WHERE r.survey = :surveyId
            GROUP BY r.answer, dateCreated
            ORDER BY dateCreated ASC
        ');

        $query->setParameter('surveyId', $surveyId);
        $result = $query->getResult();

        $labels = [];

        foreach ($result as $entry) {
            $labels[] = $entry['dateCreated'];
        }

        $labels = array_values(array_unique($labels));

        // Calculating the average answer for each date.
        // We summarize the number of answers found for each date
        // and we calculate the sum of the value of the answers given by
        // multiplying the answer value with the times the value has been
        // selected. Finally we calculate the actual average by dividing
        // the sum of the value of the answers given and the total of
        // answers given.
        $values = [];

        $totalSumAnswers = 0;
        $totalSumVotes = 0;
        foreach ($labels as $date) {
            foreach ($result as $entry) {
                if ($date === $entry['dateCreated']) {
                    $totalSumAnswers += $entry['answers'];
                    $totalSumVotes += $entry['answers'] * $entry['answer'];
                }
            }
            $values[] = $totalSumVotes / $totalSumAnswers;
            $totalSumAnswers = 0;
            $totalSumVotes = 0;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}
