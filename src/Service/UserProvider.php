<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\UserRepository;

class UserProvider
{
    public UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function getUserList()
    {
        return $this->userRepository->findAllGeneralData();
    }

    public function getUser(int $id): ?array
    {
        return $this->userRepository->findOneBy(['id' => $id]);
    }
}
