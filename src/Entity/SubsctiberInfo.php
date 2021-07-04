<?php

namespace App\Entity;

use App\Repository\SubsctiberInfoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubsctiberInfoRepository::class)
 */
class SubsctiberInfo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=MonitoringRecord::class, inversedBy="Record")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Record;

    /**
     * @ORM\Column(type="integer")
     */
    private $Count;

    /**
     * @ORM\Column(type="integer")
     */
    private $MaleCount;

    /**
     * @ORM\Column(type="integer")
     */
    private $FemaleCount;

    /**
     * @ORM\Column(type="integer")
     */
    private $ExpectedAgeCount;

    /**
     * @ORM\Column(type="integer")
     */
    private $ExpectedCountryCount;


    /**
     * @ORM\Column(type="integer")
     */
    private $ExpectedCityCount;

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

    public function getCount(): ?int
    {
        return $this->Count;
    }

    public function setCount(int $Count): self
    {
        $this->Count = $Count;

        return $this;
    }

    public function getMaleCount(): ?int
    {
        return $this->MaleCount;
    }

    public function setMaleCount(int $MaleCount): self
    {
        $this->MaleCount = $MaleCount;

        return $this;
    }

    public function getFemaleCount(): ?int
    {
        return $this->FemaleCount;
    }

    public function setFemaleCount(int $FemaleCount): self
    {
        $this->FemaleCount = $FemaleCount;

        return $this;
    }

    public function getExpectedAgeCount(): ?int
    {
        return $this->ExpectedAgeCount;
    }

    public function setExpectedAgeCount(int $ExpectedAgeCount): self
    {
        $this->ExpectedAgeCount = $ExpectedAgeCount;

        return $this;
    }

    public function getExpectedCountryCount(): ?int
    {
        return $this->ExpectedCountryCount;
    }

    public function setExpectedCountryCount(int $ExpectedCountryCount): self
    {
        $this->ExpectedCountryCount = $ExpectedCountryCount;

        return $this;
    }


    public function getExpectedCityCount(): ?int
    {
        return $this->ExpectedCityCount;
    }

    public function setExpectedCityCount(int $ExpectedCityCount): self
    {
        $this->ExpectedCityCount = $ExpectedCityCount;

        return $this;
    }
}
