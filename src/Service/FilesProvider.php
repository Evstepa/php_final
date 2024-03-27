<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\File;
use App\Entity\User;
use DirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class FilesProvider
{
    private UserProvider $userProvider;

    private string $currentUserRoot = '';

    private array $currentUserFileslist = [];

    private array $currentUserFolderslist = [];

    public function __construct()
    {
        $this->userProvider = new UserProvider();
    }

    /**
     * полный список всех каталогов с путями
     *
     * @return void
     */
    private function getFoldersList(): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->currentUserRoot)
        );
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                $this->currentUserFolderslist[] = $file->getRealpath();
            }
        }
        $this->currentUserFolderslist = array_unique($this->currentUserFolderslist);
        foreach ($this->currentUserFolderslist as $key => $value) {
            if (str_ends_with($value, UPLOAD_USER_ROOT)) {
                unset($this->currentUserFolderslist[$key]);
            }
        }
        $this->currentUserFolderslist = array_values($this->currentUserFolderslist);
    }

    /**
     * поиск папки для загрузки файла
     *
     * @param string $folderName
     * @return integer
     */
    private function searchFolder(string $folderName): int
    {
        foreach ($this->currentUserFolderslist as $key => $value) {
            if (stripos($value, $folderName)) {
                return $key;
            }
        }
        return -1;
    }

    /**
     * полный список всех файлов с путями
     *
     * @param string $currentRoot
     * @param array $searchResult
     * @return void
     */
    public function getFullFileList(): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->currentUserRoot)
        );
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $this->currentUserFileslist[] = $file;
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

        $this->currentUserRoot = $_SERVER['DOCUMENT_ROOT']
            . '/' . UPLOAD_USER_ROOT
            . '/' . $user->getFolder();

        $this->getFullFileList();
        $this->getFoldersList();

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
        foreach ($this->currentUserFileslist as $key => $value) {
            $currentUserFileslist[] = str_replace('\\', '/', $value->getPathName());
        }
        $currentUserFileslist = cleanArray(
            $currentUserFileslist,
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
            'path' => str_replace(
                '\\',
                '/',
                $this->currentUserFileslist[$userData['id'] - 1]->getPathName()
            ),
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
            || !file_exists($this->currentUserFileslist[$userData['id'] - 1]->getPathName())
        ) {
            return [
                'body' => ERROR_MESSAGES['404'],
                'status' => 404,
            ];
        }

        if (unlink($this->currentUserFileslist[$userData['id'] - 1]->getPathName())) {
            return [
                'body' => 'Файл удалён',
                'status' => 200,
            ];
        } else {
            return [
                'body' => 'Файл не может быть удалён',
                'status' => 409,
            ];
        }
    }

    /**
     * переименование файла
     *
     * @param array $userData
     * @return array
     */
    public function renameFile(array $userData): array
    {
        $answer = $this->setFileList($userData);

        if ($answer['status'] != 200) {
            return $answer;
        }

        if (mb_strlen(trim($userData['parentFolder'])) === 0) {
            $userData['parentFolder'] = $this->currentUserRoot;
        }

        $oldFileName = '';
        $newFileName = '';
        for ($i = 0; $i < count($this->currentUserFileslist); $i++) {
            if (
                $this->currentUserFileslist[$i]->getFileName() === $userData['oldName']
                &&
                str_ends_with(
                    $this->currentUserFileslist[$i]->getPath(),
                    $userData['parentFolder']
                )
            ) {
                $oldFileName = $this->currentUserFileslist[$i]->getPathName();
                $newFileName = $this->currentUserFileslist[$i]->getPath();
                break;
            }
        }
        $newFileName .= '/' . $userData['newName'];

        if ($oldFileName === '') {
            return [
                'body' => ERROR_MESSAGES['404'],
                'status' => 404,
            ];
        }

        if (file_exists($newFileName)) {
            return [
                'body' => ERROR_MESSAGES['409'],
                'status' => 409,
            ];
        }

        if (rename($oldFileName, $newFileName)) {
            return [
                'body' => 'Файл переименован',
                'status' => 200,
            ];
        } else {
            return [
                'body' => 'Файл не может быть переименован',
                'status' => 409,
            ];
        }
    }

    /**
     * загрузка файла
     *
     * @param array $userData
     * @return array
     */
    public function addFile(array $userData): array
    {
        $answer = $this->setFileList($userData);

        if ($answer['status'] != 200) {
            return $answer;
        }

        $folderId = $this->searchFolder($userData['folder']);
        $folderFrom = $userData['file']['tmp_name'];

        $folderTo = $folderId !== -1 ? $this->currentUserFolderslist[$folderId] : $this->currentUserRoot;
        $folderTo .= '/' . $userData['file']['name'];

        if (file_exists($folderTo)) {
            return [
                'body' => ERROR_MESSAGES['409'],
                'status' => 409,
            ];
        }

        if (!move_uploaded_file($folderFrom, $folderTo)) {
            return [
                'body' => ERROR_MESSAGES['460'],
                'status' => 409,
            ];
        }

        return [
            'body' => 'Файл загружен',
            'status' => 200,
        ];
    }

    /**
     * информация о папке - список файлов папки
     *
     * @param array $userData
     * @return array
     */
    public function getDirInfo(array $userData): array
    {
        $answer = $this->setFileList($userData);

        if ($answer['status'] != 200) {
            return $answer;
        }

        $currentDir = $this->currentUserFolderslist[$userData['id']];

        $dirInfo = array_values(array_diff(scandir($currentDir), array('.', '..')));

        return [
            'body' => $dirInfo,
            'status' => 200,
        ];
    }

    /**
     * создание папки
     *
     * @param array $userData
     * @return array
     */
    public function addDir(array $userData): array
    {
        $answer = $this->setFileList($userData);

        if ($answer['status'] != 200) {
            return $answer;
        }

        $newFolderName = '';
        if (mb_strlen($userData['parentFolder']) === 0) {
            $userData['parentFolder'] = $this->currentUserRoot;
            $newFolderName = $userData['parentFolder'] . '/' . $userData['folderName'];
        } else {
            $newFolderName = $this->currentUserRoot
                . '/' . $userData['parentFolder']
                . '/' . $userData['folderName'];
        }

        if (file_exists($newFolderName)) {
            return [
                'body' => ERROR_MESSAGES['409'],
                'status' => 409,
            ];
        }

        if (mkdir($newFolderName)) {
            return [
                'body' => 'Папка создана',
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
     * переименование папки
     *
     * @param array $userData
     * @return array
     */
    public function renameDir(array $userData): array
    {
        $answer = $this->setFileList($userData);

        if ($answer['status'] != 200) {
            return $answer;
        }

        $oldFolderName = '';
        $newFolderName = '';

        if (mb_strlen($userData['parentFolder']) === 0) {
            $userData['parentFolder'] = $this->currentUserRoot;
            $oldFolderName = $userData['parentFolder'] . '/' . $userData['oldName'];
            $newFolderName = $userData['parentFolder'] . '/' . $userData['newName'];
        } else {
            $oldFolderName = $this->currentUserRoot
                . '/' . $userData['parentFolder']
                . '/' . $userData['oldName'];
            $newFolderName = $this->currentUserRoot
                . '/' . $userData['parentFolder']
                . '/' . $userData['newName'];
        }

        if (file_exists($newFolderName)) {
            return [
                'body' => ERROR_MESSAGES['409'],
                'status' => 409,
            ];
        }

        if (rename($oldFolderName, $newFolderName)) {
            return [
                'body' => 'Папка переименована',
                'status' => 200,
            ];
        } else {
            return [
                'body' => 'Не удалось переименовать папку',
                'status' => 409,
            ];
        }
    }

    /**
     * удаление папки
     *
     * @param array $userData
     * @return array
     */
    public function deleteDir(array $userData): array
    {
        $answer = $this->setFileList($userData);

        if ($answer['status'] != 200) {
            return $answer;
        }

        if ($userData['id'] === 0) {
            return [
                'body' => 'Невозможно удалить корневую папку',
                'status' => 403,
            ];
        }

        if (
            $userData['id'] > count($this->currentUserFolderslist) - 1
            || (
                array_key_exists($userData['id'], $this->currentUserFolderslist)
                && !file_exists($this->currentUserFolderslist[$userData['id']])
            )
        ) {
            return [
                'body' => ERROR_MESSAGES['404'],
                'status' => 404,
            ];
        }

        if (rmdir($this->currentUserFolderslist[$userData['id']])) {
            return [
                'body' => 'Папки удалена',
                'status' => 200,
            ];
        } else {
            return [
                'body' => 'Не удалось удалить папку',
                'status' => 409,
            ];
        }
    }
}
