<?php

const ROUTES = [
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
        'method' => 'PUT',
        'controller' => 'UserController',
        'action' => 'resetUserPassword',
    ],
    // "/admin/users/list" => [
    //     'method' => 'GET',
    //     'controller' => 'AdminController',
    //     'action' => 'getUsers List',
    // ],
];
