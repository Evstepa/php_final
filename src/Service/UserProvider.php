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
                'body' => ERROR_MESSAGES['404'],
                'status' => 404,
            ];
        }

        if ($answer['body']['password'] != $userData['password']) {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }

        session_start();

        $user = new User();
        $user->fillUserData($answer['body']);

        $apiToken = new ApiToken($user);
        $this->apiTokenRepository = new ApiTokenRepository();

        $sql = sprintf("SELECT * FROM token WHERE user_id = '%d'", $user->getId());
        $answer = $this->apiTokenRepository->findOne($sql);
        if ($answer['body']) {
            $sql = sprintf("DELETE FROM token WHERE user_id = '%d'", $user->getId());
            $answer = $this->apiTokenRepository->findOne($sql);
        }

        $apiToken->setToken();
        $answer = $this->apiTokenRepository->create($apiToken);
        if ($answer['status'] != 200) {
            return [
                'body' => 'Ошибка создания токена',
                'status' => $answer['status'],
            ];
        }

        $_SESSION["currentUser"] = $apiToken->getToken();

        // var_dump($answer);
        // die();

        return $answer;
    }

    public function updateUser(array $userData): array
    {
        $answer = [];

        return $answer;
    }
}
