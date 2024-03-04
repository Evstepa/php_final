<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ApiToken;
use DateTime;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\ApiTokenRepository;

class UserProvider
{
    public UserRepository $userRepository;
    public ApiTokenRepository $apiTokenRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function getUserList()
    {
        return $this->userRepository->findAllGeneralData();
    }

    public function getUser(int $id): array
    {
        return $this->userRepository->findOneBy(['id' => $id]);
    }

    public function registerUser(array $userData): array
    {
        $answer = $this->userRepository->findOneBy(['email' => $userData['email']]);

        if ($answer['body']) {
            return [
                'body' => ERROR_MESSAGES['403'],
                'status' => 403,
            ];
        }

        $user = new User();
        $user->fillUserData($userData);
        $user->setRoles(['ROLE_USER']);
        $user->setFolder();
        $user->setUpdatedAt();
        return $this->userRepository->create($user);
    }

    public function loginUser(array $userData): array //делать
    {
        $sql = sprintf("SELECT * FROM user WHERE email = '%s'", $userData['email']);
        $answer = $this->userRepository->findOne($sql);

        if (!$answer['body']) {
            return [
                'body' => '',
                'status' => 404,
            ];
        }

        if ($answer['body']['password'] != $userData['password']) {
            return [
                'body' => '',
                'status' => 401,
            ];
        }

        $user = new User();
        $user->fillUserData($answer['body']);
        $apiToken = new ApiToken($user);
        $this->apiTokenRepository->create();
        session_start();


        return $answer;
    }
}
