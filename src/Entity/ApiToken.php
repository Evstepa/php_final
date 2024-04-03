<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;

class ApiToken
{
    private int $id;

    private string $apiToken;

    private DateTimeInterface $expiresAt;

    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Получить id токена
     *
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Получить токен
     *
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->apiToken;
    }

    /**
     * Установить токен
     *
     * @return void
     */
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
        $this->expiresAt = new DateTime('+1 hour');
    }

    /**
     * Получить время окончания действия токена
     *
     * @return DateTimeInterface
     */
    public function getExpiresAt(): DateTimeInterface
    {
        return $this->expiresAt;
    }

    /**
     * Получить пользователя
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Проверка срока действия токена
     *
     * @return boolean
     */
    public function isExpired()
    {
        return $this->getExpiresAt() < new DateTime();
    }

    /**
     * Заполнение полей данными
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
