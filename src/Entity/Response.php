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
     * Creates and returns a new instance of the Response class with the createdAt property set to the datetime
     * on creation.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Survey|null
     */
    public function getSurvey(): ?Survey
    {
        return $this->survey;
    }

    /**
     * @param Survey|null $survey
     *
     * @return Response
     */
    public function setSurvey(?Survey $survey): self
    {
        $this->survey = $survey;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAnswer(): ?int
    {
        return $this->answer;
    }

    /**
     * @param int $answer
     *
     * @return Response
     */
    public function setAnswer(int $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFollowUpAnswer(): ?int
    {
        return $this->followUpAnswer;
    }

    /**
     * @param int $followUpAnswer
     *
     * @return Response
     */
    public function setFollowUpAnswer(int $followUpAnswer): self
    {
        $this->followUpAnswer = $followUpAnswer;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface $createdAt
     *
     * @return Response
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
