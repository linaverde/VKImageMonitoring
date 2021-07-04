<?php

namespace App\Entity;

use App\Repository\GroupInfoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GroupInfoRepository::class)
 */
class GroupInfo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="Groups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Link;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Country;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $City;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $Gender;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $MinAge;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $MaxAge;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Name;

    /**
     * @ORM\OneToMany(targetEntity=MonitoringRecord::class, mappedBy="GroupLink", orphanRemoval=true)
     */
    private $Records;

    /**
     * @ORM\OneToOne(targetEntity=GroupMonitoringSettings::class, mappedBy="GroupInfo", cascade={"persist", "remove"})
     */
    private $MonitoringSettings;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $AdminToken;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $AdminName;

    public function __construct()
    {
        $this->Records = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLink(): ?string
    {
        return $this->Link;
    }

    public function setLink(string $Link): self
    {
        $this->Link = $Link;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->Country;
    }

    public function setCountry(?string $Country): self
    {
        $this->Country = $Country;

        return $this;
    }


    public function getCity(): ?string
    {
        return $this->City;
    }

    public function setCity(?string $City): self
    {
        $this->City = $City;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->Gender;
    }

    public function setGender(?string $Gender): self
    {
        $this->Gender = $Gender;

        return $this;
    }

    public function getMinAge(): ?int
    {
        return $this->MinAge;
    }

    public function setMinAge(?int $MinAge): self
    {
        $this->MinAge = $MinAge;

        return $this;
    }

    public function getMaxAge(): ?int
    {
        return $this->MaxAge;
    }

    public function setMaxAge(?int $MaxAge): self
    {
        $this->MaxAge = $MaxAge;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(?string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    /**
     * @return Collection|MonitoringRecord[]
     */
    public function getRecords(): Collection
    {
        return $this->Records;
    }

    public function addRecord(MonitoringRecord $record): self
    {
        if (!$this->Records->contains($record)) {
            $this->Records[] = $record;
            $record->setGroupLink($this);
        }

        return $this;
    }

    public function removeRecord(MonitoringRecord $record): self
    {
        if ($this->Records->removeElement($record)) {
            // set the owning side to null (unless already changed)
            if ($record->getGroupLink() === $this) {
                $record->setGroupLink(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        if ($this->Name){
            return $this->Name;
        } else {
            return $this->Link;
        }
    }

    public function getMonitoringSettings(): ?GroupMonitoringSettings
    {
        return $this->MonitoringSettings;
    }

    public function setMonitoringSettings(GroupMonitoringSettings $MonitoringSettings): self
    {
        // set the owning side of the relation if necessary
        if ($MonitoringSettings->getGroupInfo() !== $this) {
            $MonitoringSettings->setGroupInfo($this);
        }

        $this->MonitoringSettings = $MonitoringSettings;

        return $this;
    }

    public function getAdminToken(): ?string
    {
        return $this->AdminToken;
    }

    public function setAdminToken(?string $AdminToken): self
    {
        $this->AdminToken = $AdminToken;

        return $this;
    }

    public function getAdminName(): ?string
    {
        return $this->AdminName;
    }

    public function setAdminName(?string $AdminName): self
    {
        $this->AdminName = $AdminName;

        return $this;
    }
}
