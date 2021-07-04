<?php

namespace App\Entity;

use App\Repository\MonitoringRecordRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MonitoringRecordRepository::class)
 */
class MonitoringRecord
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=GroupInfo::class, inversedBy="Records")
     * @ORM\JoinColumn(nullable=false)
     */
    private $GroupLink;

    /**
     * @ORM\Column(type="datetime")
     */
    private $Time;

    /**
     * @ORM\OneToMany(targetEntity=SubsctiberInfo::class, mappedBy="Record", orphanRemoval=true)
     */
    private $Record;

    /**
     * @ORM\OneToMany(targetEntity=Publication::class, mappedBy="Record", orphanRemoval=true)
     */
    private $Publications;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="Records")
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    public function __construct()
    {
        $this->Record = new ArrayCollection();
        $this->Publications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroupLink(): ?GroupInfo
    {
        return $this->GroupLink;
    }

    public function setGroupLink(?GroupInfo $GroupLink): self
    {
        $this->GroupLink = $GroupLink;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->Time;
    }

    public function setTime(\DateTimeInterface $Time): self
    {
        $this->Time = $Time;

        return $this;
    }

    /**
     * @return Collection|SubsctiberInfo[]
     */
    public function getRecord(): Collection
    {
        return $this->Record;
    }

    public function addRecord(SubsctiberInfo $record): self
    {
        if (!$this->Record->contains($record)) {
            $this->Record[] = $record;
            $record->setRecord($this);
        }

        return $this;
    }

    public function removeRecord(SubsctiberInfo $record): self
    {
        if ($this->Record->removeElement($record)) {
            // set the owning side to null (unless already changed)
            if ($record->getRecord() === $this) {
                $record->setRecord(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Publication[]
     */
    public function getPublications(): Collection
    {
        return $this->Publications;
    }

    public function addPublication(Publication $publication): self
    {
        if (!$this->Publications->contains($publication)) {
            $this->Publications[] = $publication;
            $publication->setRecord($this);
        }

        return $this;
    }

    public function removePublication(Publication $publication): self
    {
        if ($this->Publications->removeElement($publication)) {
            // set the owning side to null (unless already changed)
            if ($publication->getRecord() === $this) {
                $publication->setRecord(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }
}
