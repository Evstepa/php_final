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
    public function getUser(array $userData): array
    {
        return $this->userProvider->getUser($userData['id']);
    }

    /**
     * route('/users/register', method='POST')
     *
     * @param array $userData
     * @return array
     */
    public function registerUser(array $userData): array
    {
        return $this->userProvider->registerUser($userData);
    }

    /**
     * route('/users/login', method='POST'))
     *
     * @param array $userData
     * @return array
     */
    public function loginUser(array $userData): array
    {
        return $this->userProvider->loginUser([
            'email' => $userData['email'],
            'password' => $userData['password'],
        ]);
    }

    /**
     * route('/users/logout', method='PUT'))
     *
     * @param array $userData
     * @return array
     */
    public function logoutUser(array $userData): array
    {
        if (
            isset($_SESSION['currentUser']) && isset($userData['token'])
            && $_SESSION['currentUser'] === $userData['token']
        ) {
            return $this->userProvider->logoutUser($userData);
        } else {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }
    }

    /**
     * route('/users/update', method='PUT'))
     *
     * @param array $userData
     * @return array
     */
    public function updateUser(array $userData): array
    {
        if (
            isset($_SESSION['currentUser']) && isset($userData['token'])
            && $_SESSION['currentUser'] === $userData['token']
        ) {
            return $this->userProvider->updateUser($userData);
        } else {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }
    }
}
