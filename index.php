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

var_dump($db->findAll());

echo "<br>--------<br>";

// var_dump($db->getConnection());

// echo "<br>--------<br>";

// $currentConnect = $db->getConnection();

// $user = [
//     'email' => 'User3@cloud.ru',
//     'password' => '111111',
//     'name' => 'User3',
//     'surname' => 'Uuser3',
//     'age' => 33,
//     'folder' => sprintf("%s-%s-%d", 'User3', 'Uuser3', rand(1, PHP_INT_MAX)),
//     'createdAt' => (new \DateTime())->format('Y-m-d H:i:s'),
//     'updatedAt' => (new \DateTime())->format('Y-m-d H:i:s'),
// ];

// $state = $currentConnect->prepare("INSERT INTO user(id, email, password, name, surname, age, folder, createdAt, updatedAt)
// values(null, :email, :password, :name, :surname, :age, :folder, :createdAt, :updatedAt)");

// $state->execute($user);

// var_dump($db->find(2));

// var_dump($db->findOneBy(['name' => 'User3']));

// var_dump($db->findBy(['name' => 'User']));

echo "<br>--------<br>";
