<?php

namespace App\Entity;

use App\Repository\UserMonitoringSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserMonitoringSettingsRepository::class)
 */
class UserMonitoringSettings
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="MonitoringSettings", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $DaysCount;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $PostsCount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(User $User): self
    {
        $this->User = $User;

        return $this;
    }

    public function getDaysCount(): ?int
    {
        return $this->DaysCount;
    }

    public function setDaysCount(?int $DaysCount): self
    {
        $this->DaysCount = $DaysCount;

        return $this;
    }

    public function getPostsCount(): ?int
    {
        return $this->PostsCount;
    }

    public function setPostsCount(?int $PostsCount): self
    {
        $this->PostsCount = $PostsCount;

        return $this;
    }
}
