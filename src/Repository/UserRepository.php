<?php

declare(strict_types=1);

namespace App\Repository;

use App\Core\Db;

final class UserRepository extends Db
{
    public function create(array $user): bool
    {
        $db = self::getInstance();
        $currentConnect = $db->getConnection();

        $state = $currentConnect->prepare("INSERT INTO user(id, email, password, name, surname, age, folder, createdAt, updatedAt)
        values(null, :email, :password, :name, :surname, :age, :folder, :createdAt, :updatedAt)");

        return $state->execute($user);
    }

    public function findAllGeneralData()
    {
        $db = self::getInstance();
        $currentConnect = $db->getConnection();

        $sql = sprintf("SELECT id, name, surname, age FROM user WHERE 1");

        $state = $currentConnect->prepare($sql);
        $state->execute();
        return $state->fetchAll();
    }
}
