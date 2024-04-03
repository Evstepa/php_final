<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;
use App\Core\Db;
use App\Entity\User;

final class UserRepository extends Db
{
    public PDO $currentConnect;

    public function __construct()
    {
        $this->currentConnect = self::getInstance()->getConnection();
    }

    /**
     * Создание пользователя в БД
     *
     * @param User $user
     * @return array
     */
    public function create(User $user): array
    {
        $answer = getSqlCreateUpdate([
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
     * Получения данных обо всех пользователях
     *
     * @return array
     */
    public function findAllGeneralData(): array
    {
        if (isset($_SESSION['role']) && in_array('ROLE_ADMIN', $_SESSION['role'])) {
            $sql = sprintf("SELECT * FROM user WHERE 1");
        } else {
            $sql = sprintf("SELECT id, name, surname, age FROM user WHERE 1");
        }
        return $this->findAll($sql);
    }

    /**
     * Получение данных об одном пользователе
     *
     * @param array $criteria
     * @return array|null
     */
    public function findOneBy(array $criteria): array
    {
        if (isset($_SESSION['role']) && in_array('ROLE_ADMIN', $_SESSION['role'])) {
            $sql = sprintf("SELECT * FROM user WHERE %s = '%s'", array_keys($criteria)[0], array_values($criteria)[0]);
        } else {
            $sql = sprintf("SELECT id, name, surname, age, folder FROM user WHERE %s = '%s'", array_keys($criteria)[0], array_values($criteria)[0]);
        }
        return $this->findOne($sql);
    }

    /**
     * Получить последнюю запись из таблицы
     *
     * @return array
     */
    public function findlLast(): array
    {
        $sql = sprintf("SELECT * FROM user ORDER BY id DESC LIMIT 1");
        $answer = $this->findOne($sql);
        return $answer;
    }

    /**
     * Изменить данные о пользователе
     *
     * @param User $user
     * @return array
     */
    public function updateUser(User $user, array $key): array
    {
        $answer = getSqlCreateUpdate([
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
