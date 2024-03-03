<?php

declare(strict_types=1);

namespace App\Service;

use DateTime;
use App\Entity\User;
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

    public function registerUser(array $user): string
    {
        $newUser = new User();
        if (isset($user['email'])) {
            $newUser->setEmail($user['email']);
        }
        if (isset($user['password'])) {
            $newUser->setPassword($user['password']);
        }
        if ($user['name']) {
            $newUser->setName($user['name']);
        }
        if (isset($user['surname'])) {
            $newUser->setSurname($user['surname']);
        }
        if (isset($user['age'])) {
            $newUser->setAge((int) $user['age']);
        }
        $newUser->setRoles(['ROLE_USER']);
        $newUser->setFolder();
        $newUser->setUpdatedAt();
        return $this->userRepository->create($newUser);
    }

    public function loginUser(array $user): ?array
    {
        $answer = $this->userRepository->findOneBy([
            'email' => $user['email'],
            // 'password' => $user['password'],
        ]);
        if (empty($answer)) {
            return [
                'body' => '',
                'status' => '404',
            ];
        }
    }
}
