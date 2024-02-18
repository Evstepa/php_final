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
        $state = $this->currentConnect->prepare($sql);
        $state->execute();
        return $state->fetchAll();
    }

    public function findById(int $id): array
    {
        $sql = sprintf("SELECT id, name, surname, age FROM user WHERE id = :id");
        $state = $this->currentConnect->prepare($sql);
        $state->execute(['id' => $id]);
        return $state->fetchAll();
    }
}
