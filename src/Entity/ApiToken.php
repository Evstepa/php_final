<?php

namespace App\Entity;

use DateTime;
use App\Entity\User;
use DateTimeInterface;
use App\Repository\ApiTokenRepository;
// use Doctrine\DBAL\Types\Types;
// use Doctrine\ORM\Mapping as ORM;

class ApiToken
{
    private int $id;

    private string $apiToken;

    private DateTimeInterface $expiresAt;

    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        // $this->setToken();
        // $this->expiresAt = new \DateTime('+1 day');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->apiToken;
    }

    public function setToken(): void
    {
        $this->apiToken = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
        // или
        // $this->apiToken = bin2hex(random_bytes (16));
        $this->expiresAt = new \DateTime('+1 hour');
    }

    public function getExpiresAt(): ?DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function isExpired()
    {
        return $this->getExpiresAt() < new \DateTime();
    }

    /**
     * заполнение полей данными
     *
     * @param array $tokenData
     * @return self
     */
    public function fillData(array $tokenData): self
    {
        $this->apiToken = $tokenData['token'];
        $this->expiresAt = new DateTime($tokenData['expiresAt']);

        return $this;
    }
}
