<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use Exception;
use DesignPatterns\Creational\Singleton\Singleton;

final class Db
{
    private object $connect;

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

    // public function getConnection()
    // {
    // }

    public function findAll(): array
    {
        $state = $this->connect->prepare("SELECT * FROM user WHERE 1");
        $state->execute();
        return $state->fetchAll();
    }

    // findOneBy();
    // findBy();
    // find().

}
