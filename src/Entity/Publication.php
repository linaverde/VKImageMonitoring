<?php

namespace App\Entity;

use App\Repository\PublicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PublicationRepository::class)
 */
class Publication
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=MonitoringRecord::class, inversedBy="Publications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Record;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Link;

    /**
     * @ORM\Column(type="integer")
     */
    private $LikesCount;

    /**
     * @ORM\Column(type="integer")
     */
    private $RepostsCount;

    /**
     * @ORM\OneToMany(targetEntity=Repost::class, mappedBy="Publication", orphanRemoval=true)
     */
    private $Reposts;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="Publication", orphanRemoval=true)
     */
    private $Comments;

    /**
     * @ORM\Column(type="integer")
     */
    private $ViewsCount;

    /**
     * @ORM\Column(type="integer")
     */
    private $CommentsCount;

    public function __construct()
    {
        $this->Reposts = new ArrayCollection();
        $this->Comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecord(): ?MonitoringRecord
    {
        return $this->Record;
    }

    public function setRecord(?MonitoringRecord $Record): self
    {
        $this->Record = $Record;

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

    public function getLikesCount(): ?int
    {
        return $this->LikesCount;
    }

    public function setLikesCount(int $LikesCount): self
    {
        $this->LikesCount = $LikesCount;

        return $this;
    }

    public function getRepostsCount(): ?int
    {
        return $this->RepostsCount;
    }

    public function setRepostsCount(int $RepostsCount): self
    {
        $this->RepostsCount = $RepostsCount;

        return $this;
    }

    /**
     * @return Collection|Repost[]
     */
    public function getReposts(): Collection
    {
        return $this->Reposts;
    }

    public function addRepost(Repost $repost): self
    {
        if (!$this->Reposts->contains($repost)) {
            $this->Reposts[] = $repost;
            $repost->setPublication($this);
        }

        return $this;
    }

    public function removeRepost(Repost $repost): self
    {
        if ($this->Reposts->removeElement($repost)) {
            // set the owning side to null (unless already changed)
            if ($repost->getPublication() === $this) {
                $repost->setPublication(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->Comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->Comments->contains($comment)) {
            $this->Comments[] = $comment;
            $comment->setPublication($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->Comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPublication() === $this) {
                $comment->setPublication(null);
            }
        }

        return $this;
    }

    public function getViewsCount(): ?int
    {
        return $this->ViewsCount;
    }

    public function setViewsCount(int $ViewsCount): self
    {
        $this->ViewsCount = $ViewsCount;

        return $this;
    }

    public function getCommentsCount(): ?int
    {
        return $this->CommentsCount;
    }

    public function setCommentsCount(int $CommentsCount): self
    {
        $this->CommentsCount = $CommentsCount;

        return $this;
    }
}
