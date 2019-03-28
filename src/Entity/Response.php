<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResponseRepository")
 */
class Response
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Survey", inversedBy="responses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $survey;

    /**
     * @ORM\Column(type="integer")
     */
    private $answer;

    /**
     * @ORM\Column(type="integer")
     */
    private $followUpAnswer;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * Response constructor.
     *
     * @param Survey    $survey
     * @param int       $answer
     * @param int       $followUpAnswer
     * @param \DateTime $dateTime
     *
     * @throws \Exception
     */
    public function __construct(Survey $survey, int $answer, int $followUpAnswer, \DateTime $dateTime)
    {
        $this->setSurvey($survey);
        $this->setAnswer($answer);
        $this->setFollowUpAnswer($followUpAnswer);
        $this->setCreatedAt($dateTime);
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Survey
     */
    public function getSurvey(): Survey
    {
        return $this->survey;
    }

    /**
     * @return int
     */
    public function getAnswer(): int
    {
        return $this->answer;
    }

    /**
     * @return int
     */
    public function getFollowUpAnswer(): int
    {
        return $this->followUpAnswer;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param Survey $survey
     *
     * @return Response
     */
    private function setSurvey(Survey $survey): self
    {
        $this->survey = $survey;

        return $this;
    }

    /**
     * @param int $answer
     *
     * @return Response
     */
    private function setAnswer(int $answer): self
    {
        if (0 > $answer || 5 < $answer) {
            throw new \InvalidArgumentException('Answer must be between 0 and 5');
        }

        $this->answer = $answer;

        return $this;
    }

    /**
     * @param int $followUpAnswer
     *
     * @return Response
     */
    private function setFollowUpAnswer(int $followUpAnswer): self
    {
        if (0 > $followUpAnswer || 5 < $followUpAnswer) {
            throw new \InvalidArgumentException('FollowUp answer must be between 0 and 5.');
        }

        $this->followUpAnswer = $followUpAnswer;

        return $this;
    }

    /**
     * @param \DateTimeInterface $createdAt
     *
     * @return Response
     */
    private function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
