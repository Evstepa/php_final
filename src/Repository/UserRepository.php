<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;
use App\Core\Db;
use PDOException;
use App\Entity\User;

final class UserRepository extends Db
{
    public PDO $currentConnect;

    public function __construct()
    {
        $this->currentConnect = self::getInstance()->getConnection();
    }

    /**
     * @param User $user
     * @return array
     */
    public function create(User $user): array
    {
        $sql = "INSERT INTO user (id, email, password, name, surname, age, folder, role, createdAt, updatedAt)
             VALUES (null, :email, :password, :name, :surname, :age, :folder, :role, :createdAt, :updatedAt)";

        $state = $this->currentConnect->prepare($sql);

        try {
            $state->execute(
                [
                    'email' => $user->getEmail(),
                    'password' => $user->getPassword(),
                    'name' => $user->getName(),
                    'surname' => $user->getSurname(),
                    'age' => $user->getAge(),
                    'folder' => $user->getFolder(),
                    'role' => implode(', ', $user->getRoles()),
                    'createdAt' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updatedAt' => $user->getUpdatedAt()->format('Y-m-d H:i:s'),
                ]
            );
        } catch (PDOException $e) {
            $state->debugDumpParams();
            return [
                'body' => $e->getMessage(),
                'status' => $e->getCode(),
            ];
        }

        return [
            'body' => 'Пользователь успешно создан',
            'status' => 200,
        ];
    }

    /**
     * @return array
     */
    public function findAllGeneralData(): array
    {
        $sql = sprintf("SELECT id, name, surname, age FROM user WHERE 1");
        $answer = $this->findAll($sql);
        return $answer;
    }

    /**
     * @param array $criteria
     * @return array|null
     */
    public function findOneBy(array $criteria): array
    {
        $sql = sprintf("SELECT id, name, surname, age FROM user WHERE %s = '%s'", array_keys($criteria)[0], array_values($criteria)[0]);
        $answer = $this->findOne($sql);
        return $answer;
    }

    /**
     * @return array
     */
    public function findlLast(): array
    {
        $sql = sprintf("SELECT * FROM user ORDER BY id DESC LIMIT 1");
        $answer = $this->findOne($sql);
        return $answer;
    }

    public function updateUser(User $user): array
    {
        $sql = "UPDATE user SET name = :name, surname = :surname, age = :age, updatedAt = :updatedAt
                WHERE id = :userId";

        $state = $this->currentConnect->prepare($sql);

        $state->bindValue(":name", $user->getName());
        $state->bindValue(":surname", $user->getsurname());
        $state->bindValue(":age", $user->getage());
        $state->bindValue(":updatedAt", $user->getupdatedAt()->format('Y-m-d H:i:s'));
        $state->bindValue(":userId", $user->getId());

        try {
            $state->execute();
        } catch (PDOException $e) {
            $state->debugDumpParams();
            return [
                'body' => $e->getMessage(),
                'status' => $e->getCode(),
            ];
        }

        return [
            'body' => 'Данные успешно изменены',
            'status' => 200,
        ];
    }
}
