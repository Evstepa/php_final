<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;
use App\Core\Db;

final class UserRepository extends Db
{
    public PDO $currentConnect;

    public function __construct()
    {
        $this->currentConnect = self::getInstance()->getConnection();
    }

    public function create(array $user): bool
    {
        $state = $this->currentConnect->prepare(
            "INSERT INTO user(id, email, password, name, surname, age, folder, createdAt, updatedAt)
             values(null, :email, :password, :name, :surname, :age, :folder, :createdAt, :updatedAt)"
        );

        return $state->execute($user);
    }

    public function findAllGeneralData(): array
    {
        $sql = sprintf("SELECT id, name, surname, age FROM user WHERE 1");
        return $this->findAll($sql);
    }

    /**
     * @param array $criteria
     * @return array|null
     */
    public function findOneBy(array $criteria): ?array
    {
        $sql = sprintf("SELECT id, name, surname, age FROM user WHERE %s = %s", array_keys($criteria)[0], array_keys($criteria)[0]);
        return $this->findOne($sql);
    }

    public function findlLast(): ?array
    {
        $sql = sprintf("SELECT * FROM user ORDER BY id DESC LIMIT 1");
        return $this->findOne($sql);
    }
}
