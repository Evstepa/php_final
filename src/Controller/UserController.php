<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Response;
use App\Entity\User;
use App\Service\UserProvider;

class UserController
{
    private UserProvider $userProvider;

    public function __construct()
    {
        $this->userProvider = new UserProvider();
    }

    /**
     * route('/users/list')
     *
     * @return string
     */
    public function getUserList(): string
    {
        return json_encode([
            'users' => $this->userProvider->getUserList()
        ]);
    }

    /**
     * route('/users/get/{id}')
     *
     * @param integer $id
     * @return string
     */
    public function getUser(array $user): string
    {
        return json_encode([
            'user' => $this->userProvider->getUser($user['id'])
        ]);
    }

    /**
     * route('/users/register')
     *
     * @param
     * @return string
     */
    public function registerUser(array $user): string
    {
        return json_encode([
            'status' => $this->userProvider->registerUser($user)
        ]);
    }

    public function loginUser(array $user): string
    {
        return json_encode([
            'token' => $this->userProvider->registerUser([
                'email' => $user['email'],
                'password' => $user['password'],
            ])
        ]);
    }
}
