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
        $answer = sqlCreateUpdate([
            'state' => $this->currentConnect,
            'query' => 'INSERT',
            'table' => 'user',
            'obj' => $user,
        ]);

        if ($answer) {
            return [
                'body' => 'Пользователь успешно создан',
                'status' => 200,
            ];
        } else {
            return [
                'body' => 'Ошибка доступа к БД',
                'status' => 403,
            ];
        }
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

    /**
     * @param User $user
     * @return array
     */
    public function updateUser(User $user, array $key): array
    {
        $answer = sqlCreateUpdate([
            'state' => $this->currentConnect,
            'query' => 'UPDATE',
            'table' => 'user',
            'obj' => $user,
            'key' => $key,
        ]);

        if ($answer) {
            return [
                'body' => 'Данные успешно изменены',
                'status' => 200,
            ];
        } else {
            return [
                'body' => 'Ошибка доступа к БД',
                'status' => 403,
            ];
        }
    }
}
