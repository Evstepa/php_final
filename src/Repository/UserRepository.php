<?php

declare(strict_types=1);

use App\Core\Db;

final class UserRepository extends Db
{

    // public function create(array $user): void
    // {
    //     $db = self::getInstance();
    //     $currentConnect = $db->getConnection();

    //     $state = $currentConnect->prepare("INSERT INTO users(id, email, password, name, surname, age, folder)
    //     values(null, :email, :password, :name, :surname, :age, :folder, )");

    //     $state->execute([
    //         'email' => $user['email'],
    //         'name' => $user['name'],
    //         'surname' => $user['surname'],
    //         'age' => $user['age'],
    //         'createdAt' => (new \DateTime())->format('Y-m-d H:i:s'),
    //         'updatedAt' => (new \DateTime())->format('Y-m-d H:i:s'),
    //     ]);
    // }
}
