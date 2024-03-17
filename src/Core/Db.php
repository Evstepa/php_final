<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use Exception;
use PDOException;

class Db
{
    protected PDO $connect;

    private static $instance = null;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->connect = new PDO(
            DB_CONFIG,
            DB_LOGIN,
            DB_PASSWORD,
            [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
    }

    private function __clone()
    {
    }

    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    public function getConnection(): PDO
    {
        return $this->connect;
    }

    /**
     * @param string $sql
     * @return array|null
     */
    public function findAll(string $sql): ?array
    {
        $db = self::getInstance();
        $state = $db->connect->prepare($sql);
        $state->execute();
        $answer = $state->fetchAll();
        $errorCode = $state->errorCode() === '00000' ? (!$answer ? 403 : 200) : $state->errorCode();
        return [
            'body' => $answer,
            'status' => $errorCode,
        ];
    }

    /**
     * @param string $sql
     * @return array|null
     */
    public function findOne(string $sql): ?array
    {
        $db = self::getInstance();
        $state = $db->connect->prepare($sql);
        $state->execute();
        $answer = $state->fetch();
        $errorCode = $state->errorCode() === '00000' ? (!$answer ? 403 : 200) : $state->errorCode();
        return [
            'body' => $answer,
            'status' => $errorCode,
        ];
    }

    /**
     * @param string $sql
     * @return array
     */
    public function deleteUser(string $sql): array
    {
        $db = self::getInstance();
        $state = $db->connect->prepare($sql);

        try {
            $state->execute();
        } catch (PDOException $e) {
            return [
                'body' => $e->getMessage(),
                'status' => $e->getCode(),
            ];
        }

        return [
            'body' => 'Пользователь успешно удалён',
            'status' => 200,
        ];
    }
}
