<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Response;
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
    public function getUser(int $id): string
    {
        return json_encode([
            'user' => $this->userProvider->getUser($id)
        ]);
    }
}
