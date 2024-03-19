<?php

namespace App\Entity;

use DateTime;

final class User
{
    private int $id;

    private string $email;

    /**
     * @var string The hashed password
     */
    private string $password;

    private array $role = [];

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
        // $this->updatedAt = new DateTime();
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
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
        $roles = $this->role;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(string $roles): self
    {
        $this->role = (array) $roles;
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
        $this->folder = sprintf(
            "%s_%s_%s",
            $this->name,
            $this->surname,
            $this->getCreatedAt()->format('y-m-d')
        );
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

    /**
     * заполнение полей объекта данными
     *
     * @param array $userData
     * @return self
     */
    public function fillUserData(array $userData): self
    {
        if (isset($userData['id'])) {
            $this->setId($userData['id']);
        }
        if (isset($userData['email'])) {
            $this->setEmail($userData['email']);
        }
        if (isset($userData['password'])) {
            $this->setPassword($userData['password']);
        }
        if (isset($userData['name'])) {
            $this->setName($userData['name']);
        }
        if (isset($userData['surname'])) {
            $this->setSurname($userData['surname']);
        }
        if (isset($userData['age'])) {
            $this->setAge((int) $userData['age']);
        }
        if (isset($userData['role'])) {
            $this->setRoles($userData['role']);
        }
        if (isset($userData['folder'])) {
            $this->folder = $userData['folder'];
        }
        $this->setUpdatedAt();

        return $this;
    }

    /**
     * выгрузка данных из полей объекта
     *
     * @return array
     */
    public function extractData(): array
    {
        $userData['id'] = isset($this->id) ? $this->getId() : null;
        $userData['email'] = isset($this->email) ? $this->getEmail() : null;
        $userData['password'] = isset($this->password) ? $this->getPassword() : null;
        $userData['role'] = isset($this->role) ? implode(', ', $this->getRoles()) : null;
        $userData['name'] = isset($this->name) ? $this->getName() : null;
        $userData['surname'] = isset($this->surname) ? $this->getSurname() : null;
        $userData['age'] = isset($this->age) ? $this->getAge() : null;
        $userData['folder'] = isset($this->folder) ? $this->getFolder() : null;
        $userData['token'] = isset($this->apiToken) ? $this->getApiToken() : null;
        $userData['createdAt'] = isset($this->createdAt) ? $this->getCreatedAt()->format('Y-m-d H:i:s') : null;
        $userData['updatedAt'] = isset($this->updatedAt) ? $this->getUpdatedAt()->format('Y-m-d H:i:s') : null;

        return $userData;
    }
}
