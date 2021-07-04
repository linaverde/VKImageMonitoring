<?php

namespace App\Entity;

use App\Repository\LogRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LogRepository::class)
 */
class Log
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $ip;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uri;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $method;

    /**
     * @ORM\Column(type="datetime")
     */
    private $request_time;

    /**
     * @ORM\Column(type="integer")
     */
    private $zone;

    /**
     * @ORM\Column(type="integer")
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $referral;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $agent;

    /**
     * @ORM\Column(type="integer")
     */
    private $bytes;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function setUri(string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function getRequestTime(): ?\DateTimeInterface
    {
        return $this->request_time;
    }

    public function setRequestTime(\DateTimeInterface $request_time): self
    {
        $this->request_time = $request_time;

        return $this;
    }

    public function getZone(): ?int
    {
        return $this->zone;
    }

    public function setZone(int $zone): self
    {
        $this->zone = $zone;

        return $this;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getReferral(): ?string
    {
        return $this->referral;
    }

    public function setReferral(string $referral): self
    {
        $this->referral = $referral;

        return $this;
    }

    public function getAgent(): ?string
    {
        return $this->agent;
    }

    public function setAgent(string $agent): self
    {
        $this->agent = $agent;

        return $this;
    }

    public function getBytes(): ?int
    {
        return $this->bytes;
    }

    public function setBytes(int $bytes): self
    {
        $this->bytes = $bytes;

        return $this;
    }
}
