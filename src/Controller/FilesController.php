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
     * получить список файлов пользователя
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
     * получить информацию о файле
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
     * удалить файл
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
     * переименовать файл
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
     * загрузить файл
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
     * информация о папке
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
     * создание папки
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
     * переименование папки
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
     * удаление папки
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
     * список пользователей, имеющих доступ к файлу
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
     * добавить доступ к файлу
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
     * прекратить доступ к файлу
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
