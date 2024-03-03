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

    public function create(User $user): string
    {
        $answer = $this->findOneBy(['email' => $user->getEmail()]);

        if (!isset($answer)) {
            return '23000';
        }

        $status = '200';

        $state = $this->currentConnect->prepare(
            "INSERT INTO user (id, email, password, name, surname, age, folder, role, createdAt, updatedAt)
             VALUES (null, :email, :password, :name, :surname, :age, :folder, :role, :createdAt, :updatedAt)"
        );

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
            $status = $e->getCode();
        }

        return $status;
    }

    public function findAllGeneralData(): ?array
    {
        $sql = sprintf("SELECT id, name, surname, age FROM user WHERE 1");
        $answer = $this->findAll($sql);
        return $answer;
    }

    /**
     * @param array $criteria
     * @return array|null
     */
    public function findOneBy(array $criteria): ?array
    {
        $sql = sprintf("SELECT id, name, surname, age FROM user WHERE %s = '%s'", array_keys($criteria)[0], array_values($criteria)[0]);
        $answer = $this->findOne($sql);
        return $answer;
    }

    public function findlLast(): ?array
    {
        $sql = sprintf("SELECT * FROM user ORDER BY id DESC LIMIT 1");
        $answer = $this->findOne($sql);
        return $answer;
    }
}
