<?php

declare(strict_types=1);

use App\Core\Db;

require_once 'vendor/autoload.php';

const URL = [
    "users/list" => "",
    "users/get/{id}" => "",
    "users/update" => "",
    "users/login" => "",
    "users/logout" => "",
    "users/reset_password" => "",
];

$db = Db::getInstance();

var_dump($db);

var_dump($db->findAll());
