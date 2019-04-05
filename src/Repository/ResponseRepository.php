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

        $totalVotes = 0;

        foreach ($values as $value) {
            $totalVotes += $value['answers'];
        }

        $newValues = [];

        for ($i = 1; $i < 6; ++$i) {
            $answers = 0;
            foreach ($values as $value) {
                if ($i === $value['answer']) {
                    $answers = floor((int) $value['answers'] / $totalVotes * 100);
                }
            }

            $newValues[] = $answers;
        }

        return $newValues;
    }
}
