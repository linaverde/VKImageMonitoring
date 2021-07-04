<?php

namespace App\Entity;

use App\Repository\GroupMonitoringSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GroupMonitoringSettingsRepository::class)
 */
class GroupMonitoringSettings
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=GroupInfo::class, inversedBy="MonitoringSettings", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $GroupInfo;

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

    public function getGroupInfo(): ?GroupInfo
    {
        return $this->GroupInfo;
    }

    public function setGroupInfo(GroupInfo $GroupInfo): self
    {
        $this->GroupInfo = $GroupInfo;

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
