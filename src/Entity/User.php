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

    private ?string $name = null;

    private ?string $surname = null;

    private string $folder;

    private ?int $age = null;

    private ApiToken $apiToken;

    private DateTime $createdAt;

    private DateTime $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getFolder(): ?string
    {
        return $this->folder;
    }

    public function setFolder(): self
    {
        $this->folder = sprintf("%s_%s_%s", $this->name, $this->surname, $this->getCreatedAt()->format('y-m-d'));

        return $this;
    }

    public function getApiToken(): ?ApiToken
    {
        return $this->apiToken;
    }

    public function setApiToken(ApiToken $apiToken): self
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(): self
    {
        $this->updatedAt = new DateTime();
        return $this;
    }

    public function fillUserData(array $userData): self
    {
        if (isset($userData['email'])) {
            $this->setEmail($userData['email']);
        }
        if (isset($userData['password'])) {
            $this->setPassword($userData['password']);
        }
        if ($userData['name']) {
            $this->setName($userData['name']);
        }
        if (isset($userData['surname'])) {
            $this->setSurname($userData['surname']);
        }
        if (isset($userData['age'])) {
            $this->setAge((int) $userData['age']);
        }
        if (isset($userData['roles'])) {
            $this->setAge((int) $userData['roles']);
        }
        if (isset($userData['folder'])) {
            $this->setAge((int) $userData['folder']);
        }
        if (isset($userData['createdAt'])) {
            $this->setAge((int) $userData['createdAt']);
        }
        if (isset($userData['updatedAt'])) {
            $this->setAge((int) $userData['updatedAt']);
        }
        return $this;
    }
}
