<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\FilesProvider;

class FilesController
{
    private FilesProvider $filesProvider;

    private UserController $userController;

    private AdminController $adminController;

    public function __construct()
    {
        $this->filesProvider = new FilesProvider();
        $this->userController = new UserController();
        $this->adminController = new AdminController();
    }

    /**
     * Получить список файлов пользователя
     * route('/files/list', method='GET')
     *
     * @return array
     */
    public function getFilesList(array $userData): array
    {
        if ($this->verify($userData['token'])) {
            return $this->filesProvider->getFilesList(['token' => $userData['token']]);
        } else {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }
    }

    /**
     * Получить информацию о файле
     * route('/files/get/{id}', method='GET')
     *
     * @return array
     */
    public function getFileInfo(array $userData): array
    {
        if ($this->verify($userData['token'])) {
            return $this->filesProvider->getFileInfo($userData);
        } else {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }
    }

    /**
     * Удалить файл
     * route('/files/remove/{id}', method='DELETE')
     *
     * @return array
     */
    public function removeFile(array $userData): array
    {
        if ($this->verify($userData['token'])) {
            return $this->filesProvider->deleteFile($userData);
        } else {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }
    }

    /**
     * Переименовать файл
     * route('/files/rename/{id}', method='PUT')
     *
     * @return array
     */
    public function renameFile(array $userData): array
    {
        if ($this->verify($userData['token'])) {
            return $this->filesProvider->renameFile($userData);
        } else {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }
    }

    /**
     * Загрузить файл
     * route('/files/add', method='POST')
     *
     * @return array
     */
    public function addFile(array $userData): array
    {
        if ($_FILES['file']['size'] > 2097152) {
            return [
                'body' => 'Слишком большой файл',
                'status' => 409,
            ];
        }

        if ($this->verify($userData['token'])) {
            $userData = array_merge($userData, $_FILES);
            return $this->filesProvider->addFile($userData);
        } else {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }
    }

    /**
     * Информация о папке
     *
     * @param array $userData
     * @return array
     */
    public function getDirInfo(array $userData): array
    {
        if ($this->verify($userData['token'])) {
            $userData = array_merge($userData, $_FILES);
            return $this->filesProvider->getDirInfo($userData);
        } else {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }
    }

    /**
     * Создание папки
     *
     * @param array $userData
     * @return array
     */
    public function addDir(array $userData): array
    {
        if ($this->verify($userData['token'])) {
            $userData = array_merge($userData, $_FILES);
            return $this->filesProvider->addDir($userData);
        } else {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }
    }

    /**
     * Переименование папки
     *
     * @param array $userData
     * @return array
     */
    public function renameDir(array $userData): array
    {
        if ($this->verify($userData['token'])) {
            $userData = array_merge($userData, $_FILES);
            return $this->filesProvider->renameDir($userData);
        } else {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }
    }

    /**
     * Удаление папки
     *
     * @param array $userData
     * @return array
     */
    public function removeDir(array $userData): array
    {
        if ($this->verify($userData['token'])) {
            $userData = array_merge($userData, $_FILES);
            return $this->filesProvider->deleteDir($userData);
        } else {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }
    }

    /**
     * Список пользователей, имеющих доступ к файлу
     *
     * @param array $userData
     * @return array
     */
    public function getShareUserList(array $userData): array
    {
        if ($this->verify($userData['token'])) {
            $userData = array_merge($userData, $_FILES);
            return $this->filesProvider->getShareUserList($userData);
        } else {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }
    }

    /**
     * Предоставить пользователю доступ к файлу
     *
     * @param array $userData
     * @return array
     */
    public function addShareFileUser(array $userData): array
    {
        if ($this->verify($userData['token'])) {
            $userData = array_merge($userData, $_FILES);
            return $this->filesProvider->addShareFileUser($userData);
        } else {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }
    }

    /**
     * Прекратить доступ к файлу
     *
     * @param array $userData
     * @return array
     */
    public function deleteShareFileUser(array $userData): array
    {
        if ($this->verify($userData['token'])) {
            $userData = array_merge($userData, $_FILES);
            return $this->filesProvider->unshareFileUser($userData);
        } else {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }
    }

    /**
     * Аутентификация пользователя
     *
     * @param string $token
     * @return boolean
     */
    private function verify(string $token): bool
    {
        if (
            isset($_SESSION['role'])
            && in_array('ROLE_ADMIN', $_SESSION['role'])
        ) {
            return $this->adminController->verifyAdminToken($token);
        } else {
            return $this->userController->verifyUserToken($token);
        }
    }
}
