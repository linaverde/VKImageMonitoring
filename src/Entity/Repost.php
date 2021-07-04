<?php

namespace App\Entity;

use App\Repository\RepostRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RepostRepository::class)
 */
class Repost
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Publication::class, inversedBy="Reposts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Publication;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Link;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $Text;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Mood;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $UserType;

    /**
     * @ORM\Column(type="integer")
     */
    private $LikesCount;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $UserGender;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $UserAge;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPublication(): ?Publication
    {
        return $this->Publication;
    }

    public function setPublication(?Publication $Publication): self
    {
        $this->Publication = $Publication;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->Link;
    }

    public function setLink(string $Link): self
    {
        $this->Link = $Link;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->Text;
    }

    public function setText(?string $Text): self
    {
        $this->Text = $Text;

        return $this;
    }

    public function getMood(): ?string
    {
        return $this->Mood;
    }

    public function setMood(?string $Mood): self
    {
        $this->Mood = $Mood;

        return $this;
    }

    public function getUserType(): ?string
    {
        return $this->UserType;
    }

    public function setUserType(string $UserType): self
    {
        $this->UserType = $UserType;

        return $this;
    }

    public function getLikesCount(): ?int
    {
        return $this->LikesCount;
    }

    public function setLikesCount(int $LikesCount): self
    {
        $this->LikesCount = $LikesCount;

        return $this;
    }

    public function getUserGender(): ?string
    {
        return $this->UserGender;
    }

    public function setUserGender(?string $UserGender): self
    {
        $this->UserGender = $UserGender;

        return $this;
    }

    public function getUserAge(): ?int
    {
        return $this->UserAge;
    }

    public function setUserAge(?int $UserAge): self
    {
        $this->UserAge = $UserAge;

        return $this;
    }
}
