<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\UserProvider;
use App\Service\FilesProvider;

class FilesController
{
    private FilesProvider $filesProvider;

    // private UserProvider $userProvider;

    public function __construct()
    {
        $this->filesProvider = new FilesProvider();
        // $this->userProvider = new UserProvider();
    }

    /**
     * route('/files/list', method='GET')
     *
     * @return array
     */
    public function getFilesList(array $userData): array
    {
        if ($_SESSION['currentUser'] === $userData['token']) {
            return $this->filesProvider->getFilesList(['token' => $userData['token']]);
        } else {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }
    }

    /**
     * route('/files/get/{id}', method='GET')
     *
     * @return array
     */
    public function getFileInfo(array $userData): array
    {
        if ($_SESSION['currentUser'] === $userData['token']) {
            return $this->filesProvider->getFileInfo($userData);
        } else {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }
    }

    /**
     * route('/files/remove/{id}', method='DELETE')
     *
     * @return array
     */
    public function removeFile(array $userData): array
    {
        if ($_SESSION['currentUser'] === $userData['token']) {
            return $this->filesProvider->deleteFile($userData);
        } else {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }
    }

    /**
     * route('/files/rename/{id}', method='PUT')
     *
     * @return array
     */
    public function renameFile(array $userData): array
    {
        if ($_SESSION['currentUser'] === $userData['token']) {
            return $this->filesProvider->renameFile($userData);
        } else {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }
    }

    /**
     * route('/files/add', method='POST')
     *
     * @return array
     */
    public function addFile(array $userData): array
    {
        if ($_SESSION['currentUser'] === $userData['token']) {
            $userData = array_merge($userData, $_FILES);
            return $this->filesProvider->addFile($userData);
        } else {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        }
    }
}
