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
    }

    /**
     * Установить id
     *
     * @param integer $id
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Получить id
     *
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Установить email
     *
     * @param string $email
     * @return self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Получить email
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Установить роли
     *
     * @param string $roles
     * @return self
     */
    public function setRoles(string $roles): self
    {
        $this->role = (array) $roles;
        return $this;
    }

    /**
     * Получить роли
     *
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->role;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Установить пароль
     *
     * @param string $password
     * @return self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Получить пароль
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Установить имя
     *
     * @param string|null $name
     * @return self
     */
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Получить имя
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Установить фамилию
     *
     * @param string|null $surname
     * @return self
     */
    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;
        return $this;
    }

    /**
     * Получить фамилию
     *
     * @return string|null
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * Установить корневую папку пользователя
     *
     * @return self
     */
    public function setFolder(): self
    {
        $this->folder = sprintf(
            "%s_%s_%d-%d-%d",
            $this->name,
            $this->surname,
            mt_rand(0, 1000),
            mt_rand(0, 1000),
            mt_rand(0, 1000)
        );
        return $this;
    }

    /**
     * Получить корневую папку пользователя
     *
     * @return string|null
     */
    public function getFolder(): ?string
    {
        return $this->folder;
    }

    /**
     * Установить токен
     *
     * @param ApiToken $apiToken
     * @return self
     */
    public function setApiToken(ApiToken $apiToken): self
    {
        $this->apiToken = $apiToken;
        return $this;
    }

    /**
     * Получить токен
     *
     * @return ApiToken|null
     */
    public function getApiToken(): ?ApiToken
    {
        return $this->apiToken;
    }

    /**
     * Установить возраст
     *
     * @param integer $age
     * @return self
     */
    public function setAge(int $age): self
    {
        $this->age = $age;
        return $this;
    }

    /**
     * Получить возраст
     *
     * @return integer|null
     */
    public function getAge(): ?int
    {
        return $this->age;
    }

    /**
     * Полчить время создания (регистрации) пользователя
     *
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * Установить время обновления данных о пользователе
     *
     * @return self
     */
    public function setUpdatedAt(): self
    {
        $this->updatedAt = new DateTime();
        return $this;
    }

    /**
     * Получить время обновления данных о пользователе
     *
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Заполнение полей объекта данными
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
            $this->setPassword(password_hash($userData['password'], PASSWORD_DEFAULT));
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
        if (isset($userData['createdAt'])) {
            $this->createdAt = new DateTime($userData['createdAt']);
        } else {
            $this->createdAt = new DateTime();
        }
        $this->setUpdatedAt();

        return $this;
    }

    /**
     * Выгрузка данных из полей объекта в массив
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
        $userData['createdAt'] = isset($this->createdAt) ? $this->getCreatedAt()->format('Y-m-d H:i:s') : null;
        $userData['updatedAt'] = isset($this->updatedAt) ? $this->getUpdatedAt()->format('Y-m-d H:i:s') : null;

        return $userData;
    }
}
