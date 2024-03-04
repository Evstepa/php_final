<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;
use App\Core\Db;
use PDOException;
use App\Entity\User;

final class ApiTokenRepository extends Db
{
    public PDO $currentConnect;

    public string $apiToken;

    public function __construct()
    {
        $this->currentConnect = self::getInstance()->getConnection();
    }

    public function create(): void
    {
        // $state = $this->currentConnect->prepare(
        //     "INSERT INTO user (id, email, password, name, surname, age, folder, role, createdAt, updatedAt)
        //      VALUES (null, :email, :password, :name, :surname, :age, :folder, :role, :createdAt, :updatedAt)"
        // );

        // try {
        //     $state->execute(
        //         [
        //             'email' => $user->getEmail(),
        //             'password' => $user->getPassword(),
        //             'name' => $user->getName(),
        //             'surname' => $user->getSurname(),
        //             'age' => $user->getAge(),
        //             'folder' => $user->getFolder(),
        //             'role' => implode(', ', $user->getRoles()),
        //             'createdAt' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
        //             'updatedAt' => $user->getUpdatedAt()->format('Y-m-d H:i:s'),
        //         ]
        //     );
        // } catch (PDOException $e) {
        //     $state->debugDumpParams();
        //     return [
        //         'body' => $e->getMessage(),
        //         'status' => $e->getCode(),
        //     ];
        // }

        // return [
        //     'body' => 'Пользователь успешно создан',
        //     'status' => 200,
        // ];
    }
}
