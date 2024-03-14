<?php

const ROUTES = [
    "/" => [
        'method' => 'GET',
        'controller' => '',
        'action' => '',
    ],
    "/users/list" => [
        'method' => 'GET',
        'controller' => 'UserController',
        'action' => 'getUserList',
    ],
    "/users/get/{id}" => [
        'method' => 'GET',
        'controller' => 'UserController',
        'action' => 'getUser',
    ],
    "/users/update" => [
        'method' => 'PUT',
        'controller' => 'UserController',
        'action' => 'updateUser',
    ],
    "/users/register" => [
        'method' => 'POST',
        'controller' => 'UserController',
        'action' => 'registerUser',
    ],
    "/users/login" => [
        'method' => 'POST',
        'controller' => 'UserController',
        'action' => 'loginUser',
    ],
    "/users/logout" => [
        'method' => 'GET',
        'controller' => 'UserController',
        'action' => 'logoutUser',
    ],
    "/users/reset_password" => [
        'method' => 'GET',
        'controller' => 'UserController',
        'action' => 'resetPassword',
    ],
    "/users/set_password" => [
        'method' => 'POST',
        'controller' => 'UserController',
        'action' => 'setNewPassword',
    ],
    // "/admin/users/list" => [
    //     'method' => 'GET',
    //     'controller' => 'AdminController',
    //     'action' => 'getUsers List',
    // ],
];
