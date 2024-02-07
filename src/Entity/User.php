<?php

namespace App\Entity;

use DateTime;

final class User
{
    private int $id;

    private string $email;

    private array $roles = [];

    /**
     * @var string The hashed password
     */
    private string $password;

    private string $name;

    private string $surname;

    private string $folder;

    private DateTime $birthday;

    private DateTime $createdAt;

    private DateTime $updatedAt;

    public function getId(): int
    {
        return $this->id;
    }
}
