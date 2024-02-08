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

var_dump($db->getConnection());

echo "<br>--------<br>";

$currentConnect = $db->getConnection();

$user = [
    'email' => 'User1@cloud.ru',
    'password' => '111111',
    'name' => 'User1',
    'surname' => 'Uuser1',
    'age' => 20,
];

$state = $currentConnect->prepare("INSERT INTO user(id, email, password, name, surname, age, folder, createdAt, updatedAt)
values(null, :email, :password, :name, :surname, :age, :folder, :createdAt, :updatedAt)");

$state->execute([
    'email' => $user['email'],
    'password' => $user['password'],
    'name' => $user['name'],
    'surname' => $user['surname'],
    'age' => $user['age'],
    'folder' => sprintf("%s-%s-%d", $user['name'], $user['surname'], rand(1, PHP_INT_MAX)),
    'createdAt' => (new \DateTime())->format('Y-m-d H:i:s'),
    'updatedAt' => (new \DateTime())->format('Y-m-d H:i:s')
]);

var_dump($db->findAll());

echo "<br>--------<br>";
