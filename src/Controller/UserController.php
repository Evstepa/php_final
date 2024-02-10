<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Response;
use App\Service\UserProvider;

class UserController
{
    /**
     * route('/user/list', name='userList')
     *
     * @return void
     */
    public function getUserList(UserProvider $userProvider): string
    {
        return json_encode([
            'users' => $userProvider->getUserList()
        ]);
    }
}
