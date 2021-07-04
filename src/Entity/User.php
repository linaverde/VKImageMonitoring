<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\OneToMany(targetEntity=UploadedLogFile::class, mappedBy="created_by")
     */
    private $Logs;

    /**
     * @ORM\OneToMany(targetEntity=GroupInfo::class, mappedBy="User", orphanRemoval=true)
     */
    private $Groups;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $OptionalName;

    /**
     * @ORM\OneToOne(targetEntity=UserMonitoringSettings::class, mappedBy="User", cascade={"persist", "remove"})
     */
    private $MonitoringSettings;

    /**
     * @ORM\OneToMany(targetEntity=MonitoringRecord::class, mappedBy="User", orphanRemoval=true)
     */
    private $Records;
    

    public function __construct()
    {
        $this->Logs = new ArrayCollection();
        $this->Groups = new ArrayCollection();
        $this->Records = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection|UploadedLogFile[]
     */
    public function getLogs(): Collection
    {
        return $this->Logs;
    }

    public function addLog(UploadedLogFile $log): self
    {
        if (!$this->Logs->contains($log)) {
            $this->Logs[] = $log;
            $log->setCreatedBy($this);
        }

        return $this;
    }

    public function removeLog(UploadedLogFile $log): self
    {
        if ($this->Logs->removeElement($log)) {
            // set the owning side to null (unless already changed)
            if ($log->getCreatedBy() === $this) {
                $log->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|GroupInfo[]
     */
    public function getGroups(): Collection
    {
        return $this->Groups;
    }

    public function addGroup(GroupInfo $group): self
    {
        if (!$this->Groups->contains($group)) {
            $this->Groups[] = $group;
            $group->setUser($this);
        }

        return $this;
    }

    public function removeGroup(GroupInfo $group): self
    {
        if ($this->Groups->removeElement($group)) {
            // set the owning side to null (unless already changed)
            if ($group->getUser() === $this) {
                $group->setUser(null);
            }
        }

        return $this;
    }

    public function getOptionalName(): ?string
    {
        return $this->OptionalName;
    }

    public function setOptionalName(?string $OptionalName): self
    {
        $this->OptionalName = $OptionalName;

        return $this;
    }

    public function __toString() {
        return $this->email;
    }

    public function getMonitoringSettings(): ?UserMonitoringSettings
    {
        return $this->MonitoringSettings;
    }

    public function setMonitoringSettings(UserMonitoringSettings $MonitoringSettings): self
    {
        // set the owning side of the relation if necessary
        if ($MonitoringSettings->getUser() !== $this) {
            $MonitoringSettings->setUser($this);
        }

        $this->MonitoringSettings = $MonitoringSettings;

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
            $record->setUser($this);
        }

        return $this;
    }

    public function removeRecord(MonitoringRecord $record): self
    {
        if ($this->Records->removeElement($record)) {
            // set the owning side to null (unless already changed)
            if ($record->getUser() === $this) {
                $record->setUser(null);
            }
        }

        return $this;
    }

}
