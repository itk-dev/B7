<?php

/**
 * @license MIT
 * @license https://opensource.org/licenses/MIT The MIT License
 */

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
    private $positive_follow_up;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $negative_follow_up;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $follow_up_text_1;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $follow_up_text_2;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $follow_up_text_3;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $follow_up_text_4;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $follow_up_text_5;

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
        return $this->positive_follow_up;
    }

    /**
     * @param string $positive_follow_up
     *
     * @return Survey
     */
    public function setPositiveFollowUp(string $positive_follow_up): self
    {
        $this->positive_follow_up = $positive_follow_up;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNegativeFollowUp(): ?string
    {
        return $this->negative_follow_up;
    }

    /**
     * @param string $negative_follow_up
     *
     * @return Survey
     */
    public function setNegativeFollowUp(string $negative_follow_up): self
    {
        $this->negative_follow_up = $negative_follow_up;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFollowUpText1(): ?string
    {
        return $this->follow_up_text_1;
    }

    /**
     * @param string $follow_up_text_1
     *
     * @return Survey
     */
    public function setFollowUpText1(string $follow_up_text_1): self
    {
        $this->follow_up_text_1 = $follow_up_text_1;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFollowUpText2(): ?string
    {
        return $this->follow_up_text_2;
    }

    /**
     * @param string $follow_up_text_2
     *
     * @return Survey
     */
    public function setFollowUpText2(string $follow_up_text_2): self
    {
        $this->follow_up_text_2 = $follow_up_text_2;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFollowUpText3(): ?string
    {
        return $this->follow_up_text_3;
    }

    /**
     * @param string $follow_up_text_3
     *
     * @return Survey
     */
    public function setFollowUpText3(string $follow_up_text_3): self
    {
        $this->follow_up_text_3 = $follow_up_text_3;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFollowUpText4(): ?string
    {
        return $this->follow_up_text_4;
    }

    /**
     * @param string|null $follow_up_text_4
     *
     * @return Survey
     */
    public function setFollowUpText4(?string $follow_up_text_4): self
    {
        $this->follow_up_text_4 = $follow_up_text_4;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFollowUpText5(): ?string
    {
        return $this->follow_up_text_5;
    }

    /**
     * @param string|null $follow_up_text_5
     *
     * @return Survey
     */
    public function setFollowUpText5(?string $follow_up_text_5): self
    {
        $this->follow_up_text_5 = $follow_up_text_5;

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
