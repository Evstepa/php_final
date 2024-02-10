<?php

declare(strict_types=1);

use App\Core\Db;
use App\Repository\UserRepository;

require_once 'vendor/autoload.php';

const URL = [
    "user/list" => "",
    "user/get/{id}" => "",
    "user/update" => "",
    "user/login" => "",
    "user/logout" => "",
    "user/reset_password" => "",
];

$db = Db::getInstance();

var_dump($db->findAll());

echo "<br>--------<br>";

// var_dump($db->getConnection());

// echo "<br>--------<br>";

// $currentConnect = $db->getConnection();

// $user = [
//     'email' => 'User4@cloud.ru',
//     'password' => '111111',
//     'name' => 'User4',
//     'surname' => 'Uuser4',
//     'age' => 44,
//     'folder' => sprintf("%s-%s-%d", 'User4', 'Uuser4', rand(1, PHP_INT_MAX)),
//     'createdAt' => (new \DateTime())->format('Y-m-d H:i:s'),
//     'updatedAt' => (new \DateTime())->format('Y-m-d H:i:s'),
// ];


// $state = $currentConnect->prepare("INSERT INTO user(id, email, password, name, surname, age, folder, createdAt, updatedAt)
// values(null, :email, :password, :name, :surname, :age, :folder, :createdAt, :updatedAt)");

// $state->execute($user);

// var_dump($db->find(2));

// var_dump($db->findOneBy(['name' => 'User3']));

// var_dump($db->findBy(['name' => 'User']));

echo __DIR__;
echo "<br>--------<br>";
var_dump($_GET);
echo "<br>--------<br>";

// require_once('./src/Template/mainpage.html');
