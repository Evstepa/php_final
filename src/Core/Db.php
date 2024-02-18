<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use Exception;

class Db
{
    public string $tableName;

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
            'mysql:host=127.0.0.1; dbname=cloud_storage; port=3306',
            'root',
            '',
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
     * @return array
     */
    public function findAll(): array
    {
        $state = $this->connect->prepare("SELECT * FROM user WHERE 1");
        $state->execute();
        return $state->fetchAll();
    }

    /**
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array
    {
        $state = $this->connect->prepare("SELECT * FROM user WHERE id = :id");
        $state->execute(['id' => $id]);
        return $state->fetch();
    }

    /**
     * @param array $criteria
     * @return array|null
     */
    public function findOneBy(array $criteria): ?array
    {
        $sql = sprintf("SELECT * FROM user WHERE %s = :%s", array_keys($criteria)[0], array_keys($criteria)[0]);
        $state = $this->connect->prepare($sql);
        $state->execute($criteria);
        return $state->fetch();
    }

    /**
     * @param array $criteria
     * @return array
     */
    public function findBy(array $criteria): ?array
    {
        $pattern = '%' . array_values($criteria)[0] . '%';
        $sql = sprintf("SELECT * FROM user WHERE %s LIKE :pattern", array_keys($criteria)[0]);
        $state = $this->connect->prepare($sql);
        $state->execute([':pattern' => $pattern]);
        return $state->fetchAll();
    }
}
