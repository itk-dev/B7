<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SurveyRepository")
 */
class Survey
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $question;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $positiveFollowUp;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $negativeFollowUp;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $followUpText1;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $followUpText2;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $followUpText3;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $followUpText4;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $followUpText5;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="surveys")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getQuestion(): ?string
    {
        return $this->question;
    }

    /**
     * @param string $question
     *
     * @return Survey
     */
    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Survey
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPositiveFollowUp(): ?string
    {
        return $this->positiveFollowUp;
    }

    /**
     * @param string $positiveFollowUp
     *
     * @return Survey
     */
    public function setPositiveFollowUp(string $positiveFollowUp): self
    {
        $this->positiveFollowUp = $positiveFollowUp;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNegativeFollowUp(): ?string
    {
        return $this->negativeFollowUp;
    }

    /**
     * @param string $negativeFollowUp
     *
     * @return Survey
     */
    public function setNegativeFollowUp(string $negativeFollowUp): self
    {
        $this->negativeFollowUp = $negativeFollowUp;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFollowUpText1(): ?string
    {
        return $this->followUpText1;
    }

    /**
     * @param string $followUpText1
     *
     * @return Survey
     */
    public function setFollowUpText1(string $followUpText1): self
    {
        $this->followUpText1 = $followUpText1;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFollowUpText2(): ?string
    {
        return $this->followUpText2;
    }

    /**
     * @param string $followUpText2
     *
     * @return Survey
     */
    public function setFollowUpText2(string $followUpText2): self
    {
        $this->followUpText2 = $followUpText2;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFollowUpText3(): ?string
    {
        return $this->followUpText3;
    }

    /**
     * @param string $followUpText3
     *
     * @return Survey
     */
    public function setFollowUpText3(string $followUpText3): self
    {
        $this->followUpText3 = $followUpText3;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFollowUpText4(): ?string
    {
        return $this->followUpText4;
    }

    /**
     * @param string|null $followUpText4
     *
     * @return Survey
     */
    public function setFollowUpText4(?string $followUpText4): self
    {
        $this->followUpText4 = $followUpText4;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFollowUpText5(): ?string
    {
        return $this->followUpText5;
    }

    /**
     * @param string|null $followUpText5
     *
     * @return Survey
     */
    public function setFollowUpText5(?string $followUpText5): self
    {
        $this->followUpText5 = $followUpText5;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     *
     * @return Survey
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
