<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ApiToken;
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
        $this->apiTokenRepository = new ApiTokenRepository();
    }

    /**
     * запрос данных всех пользователей
     *
     * @return void
     */
    public function getUserList(): array
    {
        return $this->userRepository->findAllGeneralData();
    }

    /**
     * запрос данных одного пользователя
     *
     * @param integer $id
     * @return array
     */
    public function getUser(array $criteria): array
    {
        return $this->userRepository->findOneBy($criteria);
    }

    /**
     * регистрация в системе
     *
     * @param array $userData
     * @return array
     */
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
        $user->setRoles('ROLE_USER');
        $user->setFolder();

        $answer = $this->userRepository->create($user);

        if ($answer['status'] !== 200) {
            return [
                'body' => 'Ошибка доступа к БД',
                'status' => 409,
            ];
        }

        $parentFolder = $_SERVER['DOCUMENT_ROOT'] . '/' . UPLOAD_USER_ROOT;
        $userFolderName = $parentFolder . '/' . $user->getFolder();
        if (mkdir($userFolderName)) {
            return [
                'body' => 'Пользователь успешно рагеристриован. Папка создана',
                'status' => 200,
            ];
        } else {
            return [
                'body' => 'Не удалось создать папку',
                'status' => 409,
            ];
        }
    }

    /**
     * вход по паролю
     *
     * @param array $userData
     * @return array
     */
    public function loginUser(array $userData): array
    {
        $sql = sprintf("SELECT * FROM user WHERE email = '%s'", $userData['email']);
        $answer = $this->userRepository->findOne($sql);

        if (!$answer['body']) {
            return [
                'body' => ERROR_MESSAGES['404'],
                'status' => 404,
            ];
        }

        if (!($answer['body']
            && password_verify($userData['password'], $answer['body']['password']))) {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }

        $user = new User();
        $user->fillUserData($answer['body']);

        $answer = $this->apiTokenRepository->deleteToken($user);

        $apiToken = new ApiToken($user);
        $apiToken->setToken();

        $answer = $this->apiTokenRepository->create($apiToken);
        if ($answer['status'] != 200) {
            return [
                'body' => 'Ошибка создания токена',
                'status' => $answer['status'],
            ];
        }

        $_SESSION['currentUser'] = $apiToken->getToken();
        $_SESSION['role'] = $user->getRoles();

        return $answer;
    }

    /**
     * выход из системы
     *
     * @param array $userData
     * @return array
     */
    public function logoutUser(array $userData): array
    {
        $answer = $this->apiTokenRepository->logout($userData);

        unset($_SESSION['currentUser']);
        unset($_SESSION['role']);

        // или
        // sessin_destroy();

        header('Location: index.php');

        return $answer;
    }

    /**
     * обновление данных в таблице
     *
     * @param array $userData
     * @return array
     */
    public function updateUser(array $userData): array
    {
        $answer = $this->getToken($userData['token']);

        if (!$answer['body']) {
            return [
                'body' => 'Ошибка чтения токена',
                'status' => $answer['status'],
            ];
        }
        //добавить проверку срока действия токена
        $user = new User();
        if (isset($userData['id'])) {
            $user->setId($userData['id']);
        } else {
            $user->setId($answer['body']['user_id']);
        }
        $user->fillUserData($userData);

        $answer = $this->userRepository->updateUser($user, ['id' => $user->getId()]);

        return $answer;
    }

    /**
     * отправка письма для сброса пароля
     *
     * @param array $userData
     * @return array
     */
    public function resetPassword(array $userData): array
    {
        $sql = sprintf("SELECT * FROM user WHERE email = '%s'", $userData['email']);
        $answer = $this->userRepository->findOne($sql);

        if (!$answer['body']) {
            return [
                'body' => ERROR_MESSAGES['404'],
                'status' => 404,
            ];
        }

        $user = new User();
        $user->fillUserData($answer['body']);

        $answer = $this->apiTokenRepository->deleteToken($user);

        $apiToken = new ApiToken($user);
        $apiToken->setToken();
        $answer = $this->apiTokenRepository->create($apiToken);

        $title = 'Вы запросили сброс пароля на http://cloud-storage.ru';
        $message = 'Для сброса пароля Вам нужно пройти по ссылке <a href="'
            . CLOUD_HOST . '/users/reset_password?token=' . $apiToken->getToken()
            . '">Сбросить пароль' . '</a>';

        $answer = sendMessageMail($userData['email'], MAIL_AUTOR, $title, $message);

        $_SESSION["resetToken"] = $apiToken->getToken();

        //Перенаправляем пользователя на нужную нам страницу
        // header('Location:' . CLOUD_HOST . '/users/set_password');

        return [
            'body' => 'Сообщение успешно отправлено',
            'status' => 200,
        ];
    }

    /**
     * установка нового пароля
     *
     * @param array $userData
     * @return array
     */
    public function setNewPassword(array $userData): array
    {
        $answer = $this->getToken($userData['token']);

        if (!$answer['body']) {
            return [
                'body' => 'Ошибка чтения токена',
                'status' => $answer['status'],
            ];
        }

        $user = new User();
        $user->setId($answer['body']['user_id']);
        $user->fillUserData($userData);

        if ($this->verifyToken($user, $answer['body'])) {
            return [
                'body' => 'Срок действия токена истёк',
                'status' => 401,
            ];
        }

        $user->setPassword($userData['password']);

        $answer = $this->userRepository->updateUser($user, ['id' => $answer['body']['user_id']]);
        $this->apiTokenRepository->deleteToken($user);

        return [
            'body' => 'Новый пароль установлен',
            'status' => 200,
        ];
    }

    /**
     * удаление файла
     * @param integer $id
     * @return array
     */
    public function deleteUser(int $id): array
    {
        $sql = sprintf("SELECT `folder` FROM `user` WHERE `id` = %d", $id);
        $answer = $this->userRepository->findOne($sql);
        if (!$answer['body']) {
            return [
                'body' => ERROR_MESSAGES['404'],
                'status' => 404,
            ];
        }
        $userFolderName =
            $_SERVER['DOCUMENT_ROOT']
            . '/' . UPLOAD_USER_ROOT
            . '/' . $answer['body']['folder'];

        if (!rmdir($userFolderName)) {
            return [
                'body' => 'Не удалось удалить папку и пользователя',
                'status' => 409,
            ];
        }

        $sql = sprintf("DELETE FROM user WHERE id = %d", $id);
        return $this->userRepository->delete($sql);
    }

    /**
     * запрос к таблице token
     *
     * @param string $token
     * @return array|null
     */
    public function getToken(string $token): ?array
    {
        $sql = sprintf("SELECT * FROM token WHERE token = '%s'", $token);
        return $this->apiTokenRepository->findOne($sql);
    }

    /**
     * получить пользователя по его токену
     *
     * @param string $token
     * @return array
     */
    public function getTokenUser(string $token): array
    {
        $sql = sprintf(
            "SELECT * FROM `token`
            JOIN `user` ON `user`.`id` = `token`.`user_id`
            WHERE `token`.`token`='%s'",
            $token
        );
        return $this->apiTokenRepository->findOne($sql);
    }

    /**
     * верификация токена
     * @param User $user
     * @param array $tokenData
     * @return boolean
     */
    public function verifyToken(User $user, array $tokenData): bool
    {
        $resetToken = new ApiToken($user);
        $resetToken->fillData($tokenData);

        return (!$resetToken->isExpired());
    }
}
