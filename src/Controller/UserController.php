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
     * @return array
     */
    public function getUserList(): array
    {
        return $this->userProvider->getUserList();
    }

    /**
     * route('/users/get/{id}')
     *
     * @param integer $id
     * @return array
     */
    public function getUser(array $user): array
    {
        return $this->userProvider->getUser($user['id']);
    }

    /**
     * route('/users/register')
     *
     * @param
     * @return array
     */
    public function registerUser(array $user): array
    {
        return $this->userProvider->registerUser($user);
    }

    public function loginUser(array $user): array
    {
        return $this->userProvider->loginUser([
            'email' => $user['email'],
            'password' => $user['password'],
        ]);
    }
}
