<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\UserProvider;

class AdminController
{
    private UserProvider $userProvider;

    public function __construct()
    {
        $this->userProvider = new UserProvider();
    }

    /**
     * route('/admin/users/list', method='GET')
     *
     * @return array
     */
    public function getUsersList(array $userData): array
    {
        if (
            isset($_SESSION['role'])
            && in_array('ROLE_ADMIN', $_SESSION['role'])
            && $this->verifyAdminToken($userData['token'])
        ) {
            return $this->userProvider->getUserList();
        } else {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }
    }

    /**
     * route('/admin/users/get/{id}', method='GET')
     *
     * @param integer $id
     * @return array
     */
    public function getUser(array $userData): array
    {
        if (
            isset($_SESSION['role'])
            && in_array('ROLE_ADMIN', $_SESSION['role'])
            && $this->verifyAdminToken($userData['token'])
        ) {
            return $this->userProvider->getUser(['id' => $userData['id']]);
        } else {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }
    }

    /**
     * route('/admin/users/delete/{id}', method='DELETE'))
     *
     * @param array $userData
     * @return array
     */
    public function deleteUser(array $userData): array
    {
        if (
            isset($_SESSION['role'])
            && in_array('ROLE_ADMIN', $_SESSION['role'])
            && $this->verifyAdminToken($userData['token'])
        ) {
            return $this->userProvider->deleteUser($userData['id']);
        } else {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }
    }

    /**
     * route('/admin/users/update/{id}', method='PUT'))
     *
     * @param array $userData
     * @return array
     */
    public function updateUser(array $userData): array
    {
        if (
            isset($_SESSION['role'])
            && in_array('ROLE_ADMIN', $_SESSION['role'])
            && $this->verifyAdminToken($userData['token'])
        ) {
            return $this->userProvider->updateUser($userData);
        } else {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }
    }

    /**
     * @param string $requestToken
     * @return boolean
     */
    public function verifyAdminToken(string $requestToken): bool
    {
        $user = new User();
        $user->fillUserData($this->userProvider->getUser(['role' => 'ROLE_ADMIN'])['body']);

        $sessionToken = isset($_SESSION['currentUser']) ? $_SESSION['currentUser'] : '';
        $adminToken = $this->userProvider->getToken($requestToken)['body'];

        return (
            $user->getId() === $adminToken['user_id']
            && $requestToken === $sessionToken
            && $this->userProvider->verifyToken($user, $adminToken)
        );
    }
}
