<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\Column(type="string", length=50)
     */
    private $followUpText1;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $followUpText2;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $followUpText3;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $followUpText4;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $followUpText5;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="surveys")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Response", mappedBy="survey")
     */
    private $responses;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $neutralFollowUp;

    public function __construct()
    {
        $this->responses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    /**
     * @return Survey
     */
    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return Survey
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPositiveFollowUp(): ?string
    {
        return $this->positiveFollowUp;
    }

    /**
     * @return Survey
     */
    public function setPositiveFollowUp(string $positiveFollowUp): self
    {
        $this->positiveFollowUp = $positiveFollowUp;

        return $this;
    }

    public function getNegativeFollowUp(): ?string
    {
        return $this->negativeFollowUp;
    }

    /**
     * @return Survey
     */
    public function setNegativeFollowUp(string $negativeFollowUp): self
    {
        $this->negativeFollowUp = $negativeFollowUp;

        return $this;
    }

    public function getFollowUpText1(): ?string
    {
        return $this->followUpText1;
    }

    /**
     * @return Survey
     */
    public function setFollowUpText1(string $followUpText1): self
    {
        $this->followUpText1 = $followUpText1;

        return $this;
    }

    public function getFollowUpText2(): ?string
    {
        return $this->followUpText2;
    }

    /**
     * @return Survey
     */
    public function setFollowUpText2(string $followUpText2): self
    {
        $this->followUpText2 = $followUpText2;

        return $this;
    }

    public function getFollowUpText3(): ?string
    {
        return $this->followUpText3;
    }

    /**
     * @return Survey
     */
    public function setFollowUpText3(string $followUpText3): self
    {
        $this->followUpText3 = $followUpText3;

        return $this;
    }

    public function getFollowUpText4(): ?string
    {
        return $this->followUpText4;
    }

    /**
     * @return Survey
     */
    public function setFollowUpText4(?string $followUpText4): self
    {
        $this->followUpText4 = $followUpText4;

        return $this;
    }

    public function getFollowUpText5(): ?string
    {
        return $this->followUpText5;
    }

    /**
     * @return Survey
     */
    public function setFollowUpText5(?string $followUpText5): self
    {
        $this->followUpText5 = $followUpText5;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return Survey
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Response[]
     */
    public function getResponses(): Collection
    {
        return $this->responses;
    }

    /**
     * @return Survey
     */
    public function addResponse(Response $response): self
    {
        if (!$this->responses->contains($response)) {
            $this->responses[] = $response;
            $response->setSurvey($this);
        }

        return $this;
    }

    /**
     * @return Survey
     */
    public function removeResponse(Response $response): self
    {
        if ($this->responses->contains($response)) {
            $this->responses->removeElement($response);
            // set the owning side to null (unless already changed)
            if ($response->getSurvey() === $this) {
                $response->setSurvey(null);
            }
        }

        return $this;
    }

    public function getNeutralFollowUp(): ?string
    {
        return $this->neutralFollowUp;
    }

    public function setNeutralFollowUp(string $neutralFollowUp): self
    {
        $this->neutralFollowUp = $neutralFollowUp;

        return $this;
    }
}
