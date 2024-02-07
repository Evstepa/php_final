<?php

namespace App\Entity;

use App\Entity\User;
use App\Repository\ApiTokenRepository;
// use Doctrine\DBAL\Types\Types;
// use Doctrine\ORM\Mapping as ORM;

class ApiToken
{
    private int $id;

    private string $token;

    // private DateTimeInterface $expiresAt;

    private ?User $user = null;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->token = sha1(uniqid('token'));
        // $this->expiresAt = new \DateTime('+1 day');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    // public function getExpiresAt(): ?\DateTimeInterface
    // {
    //     return $this->expiresAt;
    // }

    public function getUser(): ?User
    {
        return $this->user;
    }

    // public function isExpired()
    // {
    //     return $this->getExpiresAt() <= new \DateTime();
    // }
}
