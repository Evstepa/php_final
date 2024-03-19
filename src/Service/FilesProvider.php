<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\File;
use App\Entity\User;

class FilesProvider
{
    private UserProvider $userProvider;

    private string $currentUserRoot = '';

    private array $currentUserFileslist = [];

    public function __construct()
    {
        $this->userProvider = new UserProvider();
    }

    /**
     * полный список всех файлов с путями
     *
     * @param string $currentRoot
     * @param array $searchResult
     * @return void
     */
    public function getFullFileList(string $currentRoot, array &$searchResult): void
    {
        if (!is_dir($currentRoot)) {
            $searchResult[] = $currentRoot;
            return;
        }

        $files = scandir($currentRoot);
        for ($i = 0; $i < count($files); $i++) {
            if (
                is_dir($currentRoot)
                && !(strrpos($currentRoot, '/.') || strrpos($currentRoot, '/..'))
            ) {
                $this->getFullFileList($currentRoot . "/" . $files[$i], $searchResult);
            }
        }
    }

    /**
     * верификация пользователя и установка значений полей $currentUserRoot и $currentUserFileslist
     *
     * @param array $userData
     * @return array
     */
    private function setFileList(array $userData): array
    {
        $tokenBd = $this->userProvider->getToken($userData['token'])['body'];
        $userBd = $this->userProvider->getUser(['id' => $tokenBd['user_id']])['body'];
        $user = new User();
        $user->fillUserData($userBd);

        if (!$this->userProvider->verifyToken($user, $tokenBd)) {
            return [
                'body' => ERROR_MESSAGES['401'],
                'status' => 401,
            ];
        };

        $this->currentUserRoot = $_SERVER['DOCUMENT_ROOT'] . '/' . UPLOAD_USER_ROOT . '/' . $user->getFolder();

        $currentUserFileslist = [];
        $this->getFullFileList($this->currentUserRoot, $currentUserFileslist);
        $this->currentUserFileslist = $currentUserFileslist;

        return [
            'body' => 'OK',
            'status' => 200,
        ];
    }

    /**
     * список всех файлов текущего пользователя
     *
     * @param array $userData
     * @return array
     */
    public function getFilesList(array $userData): array
    {
        $answer = $this->setFileList($userData);

        if ($answer['status'] != 200) {
            return $answer;
        }

        $currentUserFileslist = [];
        $currentUserFileslist = cleanArray(
            $this->currentUserFileslist,
            $this->currentUserRoot . '/'
        );

        return [
            'body' => $currentUserFileslist,
            'status' => 200,
        ];
    }

    /**
     * получение информации о файле по его id
     *
     * @param array $userData
     * @return array
     */
    public function getFileInfo(array $userData): array
    {
        $answer = $this->setFileList($userData);

        if ($answer['status'] != 200) {
            return $answer;
        }

        if ($userData['id'] > count($this->currentUserFileslist)) {
            return [
                'body' => ERROR_MESSAGES['404'],
                'status' => 404,
            ];
        }

        $currentFile = new File([
            'id' => $userData['id'],
            'path' => $this->currentUserFileslist[$userData['id'] - 1],
        ]);

        return [
            'body' => $currentFile->exportData(),
            'status' => 200,
        ];
    }

    /**
     * удаление файла
     *
     * @param array $userData
     * @return array
     */
    public function deleteFile(array $userData): array
    {
        $answer = $this->setFileList($userData);

        if ($answer['status'] != 200) {
            return $answer;
        }

        if (
            $userData['id'] > count($this->currentUserFileslist)
            || !file_exists($this->currentUserFileslist[$userData['id'] - 1])
        ) {
            return [
                'body' => ERROR_MESSAGES['404'],
                'status' => 404,
            ];
        }

        unlink($this->currentUserFileslist[$userData['id'] - 1]);

        return [
            'body' => 'Файл удалён',
            'status' => 200,
        ];
    }

    public function renameFile(array $userData): array
    {
        $answer = $this->setFileList($userData);

        if ($answer['status'] != 200) {
            return $answer;
        }

        if (
            $userData['id'] > count($this->currentUserFileslist)
            || !file_exists($this->currentUserFileslist[$userData['id'] - 1])
        ) {
            return [
                'body' => ERROR_MESSAGES['404'],
                'status' => 404,
            ];
        }

        $currentFile = new File([
            'id' => $userData['id'],
            'path' => $this->currentUserFileslist[$userData['id'] - 1],
        ]);

        $newName = $this->currentUserRoot . $currentFile->getFolder() . '/' . $userData['name'];
        if (file_exists($newName)) {
            return [
                'body' => ERROR_MESSAGES['409'],
                'status' => 409,
            ];
        }
        rename(
            $this->currentUserFileslist[$userData['id'] - 1],
            $newName
        );

        return [
            'body' => 'Файл переименован',
            'status' => 200,
        ];
    }
}
