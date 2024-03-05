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
     * route('/users/list', method='GET')
     *
     * @return array
     */
    public function getUserList(): array
    {
        return $this->userProvider->getUserList();
    }

    /**
     * route('/users/get/{id}', method='GET')
     *
     * @param integer $id
     * @return array
     */
    public function getUser(array $user): array
    {
        return $this->userProvider->getUser($user['id']);
    }

    /**
     * route('/users/register', method='POST')
     *
     * @param
     * @return array
     */
    public function registerUser(array $user): array
    {
        return $this->userProvider->registerUser($user);
    }

    /**
     * route('/users/login', method='POST'))
     *
     * @param array $user
     * @return array
     */
    public function loginUser(array $user): array
    {
        return $this->userProvider->loginUser([
            'email' => $user['email'],
            'password' => $user['password'],
        ]);
    }

    public function updateUser(array $user): array
    {
        return $this->userProvider->updateUser($user);
    }
}
