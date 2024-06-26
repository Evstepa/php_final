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
    "/users/search/{email}" => [
        'method' => 'GET',
        'controller' => 'UserController',
        'action' => 'searchUserByEmail',
    ],
    "/admin/users/list" => [
        'method' => 'GET',
        'controller' => 'AdminController',
        'action' => 'getUsersList',
    ],
    "/admin/users/get/{id}" => [
        'method' => 'GET',
        'controller' => 'AdminController',
        'action' => 'getUser',
    ],
    "/admin/users/delete/{id}" => [
        'method' => 'DELETE',
        'controller' => 'AdminController',
        'action' => 'deleteUser',
    ],
    "/admin/users/update/{id}" => [
        'method' => 'PUT',
        'controller' => 'AdminController',
        'action' => 'updateUser',
    ],
    "/files/list" => [
        'method' => 'GET',
        'controller' => 'FilesController',
        'action' => 'getFilesList',
    ],
    "/files/get/{id}" => [
        'method' => 'GET',
        'controller' => 'FilesController',
        'action' => 'getFileInfo',
    ],
    "/files/add" => [
        'method' => 'POST',
        'controller' => 'FilesController',
        'action' => 'addFile',
    ],
    "/files/rename" => [
        'method' => 'PUT',
        'controller' => 'FilesController',
        'action' => 'renameFile',
    ],
    "/files/remove/{id}" => [
        'method' => 'DELETE',
        'controller' => 'FilesController',
        'action' => 'removeFile',
    ],
    "/files/share/{id}" => [
        'method' => 'GET',
        'controller' => 'FilesController',
        'action' => 'getShareUserList',
    ],
    "/files/share/{id}/{user_id}" => [
        'method' => 'PUT',
        'controller' => 'FilesController',
        'action' => 'addShareFileUser',
    ],
    "/files/unshare/{id}/{user_id}" => [
        'method' => 'DELETE',
        'controller' => 'FilesController',
        'action' => 'deleteShareFileUser',
    ],
    "/directories/get/{id}" => [
        'method' => 'GET',
        'controller' => 'FilesController',
        'action' => 'getDirInfo',
    ],
    "/directories/add" => [
        'method' => 'POST',
        'controller' => 'FilesController',
        'action' => 'addDir',
    ],
    "/directories/rename" => [
        'method' => 'PUT',
        'controller' => 'FilesController',
        'action' => 'renameDir',
    ],
    "/directories/remove/{id}" => [
        'method' => 'DELETE',
        'controller' => 'FilesController',
        'action' => 'removeDir',
    ],
];
