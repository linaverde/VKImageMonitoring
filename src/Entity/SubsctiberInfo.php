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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $SmallerAgeCount;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $BiggerAgeCount;

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

    public function getSmallerAgeCount(): ?int
    {
        return $this->SmallerAgeCount;
    }

    public function setSmallerAgeCount(?int $SmallerAgeCount): self
    {
        $this->SmallerAgeCount = $SmallerAgeCount;

        return $this;
    }

    public function getBiggerAgeCount(): ?int
    {
        return $this->BiggerAgeCount;
    }

    public function setBiggerAgeCount(?int $BiggerAgeCount): self
    {
        $this->BiggerAgeCount = $BiggerAgeCount;

        return $this;
    }
}
