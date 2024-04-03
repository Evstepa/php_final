<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\File;
use App\Entity\User;
use App\Repository\FileRepository;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class FilesProvider
{
    private UserProvider $userProvider;

    private FileRepository $fileRepository;

    private string $currentUserRoot = '';

    private array $currentUserFileslist = [];

    private array $currentUserFolderslist = [];

    public function __construct()
    {
        $this->userProvider = new UserProvider();
        $this->fileRepository = new FileRepository();
    }

    /**
     * Полный список всех каталогов с путями
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
     * Поиск папки для загрузки файла
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
     * Полный список всех файлов с путями
     *
     * @param int $userId
     * @return void
     */
    public function getFullFileList(int $userId): void
    {
        $sql = sprintf(
            "SELECT `file`.`id`, `file`.`full_path`
            FROM `access`
            JOIN `file` ON `file`.`id`= `access`.`file_id`
            WHERE user_id = %d",
            $userId
        );

        $answer = $this->fileRepository->findAll($sql);
        if ($answer['status'] !== 200) {
            $this->currentUserFileslist = [];
        }

        $currentUserFileslist = [];
        foreach ($answer['body'] as $key => $value) {
            $currentUserFileslist[] = new File([
                'id' => $value['id'],
                'path' => $_SERVER['DOCUMENT_ROOT'] . '/' . UPLOAD_USER_ROOT
                    . $value['full_path'],
            ]);
        }
        $this->currentUserFileslist = $currentUserFileslist;
    }

    /**
     * Верификация пользователя
     * и установка значений полей $currentUserRoot и $currentUserFileslist
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

        $this->getFullFileList($user->getId());
        $this->getFoldersList();

        return [
            'body' => [
                'userId' => $user->getId(),
            ],
            'status' => 200,
        ];
    }

    /**
     * Список всех файлов текущего пользователя
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

        $currentFileList = [];
        foreach ($this->currentUserFileslist as $key => $value) {
            if ($value->getFolder() !== '/') {
                $currentFileList[] = $value->getFolder() . '/' . $value->getName();
            } else {
                $currentFileList[] = $value->getFolder() . $value->getName();
            }
        }

        return [
            'body' => $currentFileList,
            'status' => 200,
        ];
    }

    /**
     * Получение информации о файле по его id
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

        $currentFile = $this->currentUserFileslist[$userData['id'] - 1];

        return [
            'body' => $currentFile->exportData(),
            'status' => 200,
        ];
    }

    /**
     * Удаление файла
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
            || !file_exists($this->currentUserFileslist[$userData['id'] - 1]->getPath())
        ) {
            return [
                'body' => ERROR_MESSAGES['404'],
                'status' => 404,
            ];
        }

        if (unlink($this->currentUserFileslist[$userData['id'] - 1]->getPath())) {
            $this->fileRepository->remove($this->currentUserFileslist[$userData['id'] - 1]);
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
     * Переименование файла
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
            $userData['parentFolder'] = '/';
        }

        $oldFileName = '';
        $newFileName = '';
        $fileId = null;

        for ($i = 0; $i < count($this->currentUserFileslist); $i++) {
            if (
                $this->currentUserFileslist[$i]->getName() === $userData['oldName']
                &&
                str_ends_with(
                    $this->currentUserFileslist[$i]->getFolder(),
                    $userData['parentFolder']
                )
            ) {
                $fileId = $i;
                $oldFileName = $this->currentUserFileslist[$i]->getPath();
                $newFileName = $this->currentUserFileslist[$i]->getShortPath();
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
            $this->fileRepository->rename(
                $this->currentUserFileslist[$fileId],
                implode('/', array_slice(explode('/', $newFileName), 5))
            );
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
     * Загрузка файла
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
        if ($folderId === -1) {
            return [
                'body' => 'Указанная папка не существует',
                'status' => 404,
            ];
        }

        $folderFrom = $userData['file']['tmp_name'];

        $folderTo =
            $folderId !== -1
            ? $this->currentUserFolderslist[$folderId]
            : $this->currentUserRoot;
        $folderTo .= '\\' . $userData['file']['name'];
        $shotFolderTo = '\\' . implode('\\', array_slice(explode('\\', $folderTo), 5));
        $shotFolderTo = str_replace('\\', '/', $shotFolderTo);

        if (file_exists($folderTo)) {
            return [
                'body' => ERROR_MESSAGES['409'],
                'status' => 409,
            ];
        }

        if (file_exists($folderTo)) {
            return [
                'body' => ERROR_MESSAGES['409'],
                'status' => 409,
            ];
        }

        if (move_uploaded_file($folderFrom, $folderTo)) {
            $answer = $this->fileRepository->addFile([
                'userId' => $answer['body']['userId'],
                'fullPath' => $shotFolderTo,
            ]);
            if ($answer['status'] !== 200) {
                return [
                    'body' => 'Ошибка доступа к БД',
                    'status' => 409,
                ];
            }
            return [
                'body' => 'Файл загружен',
                'status' => 200,
            ];
        } else {
            return [
                'body' => ERROR_MESSAGES['460'],
                'status' => 409,
            ];
        }
    }

    /**
     * Информация о папке - список файлов папки
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
     * Создание папки
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
     * Переименование папки
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
     * Удаление папки
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

    /**
     * Список пользователей, имеющих доступ к файлу
     *
     * @param array $userData
     * @return array
     */
    public function getShareUserList(array $userData): array
    {
        $answer = $this->setFileList($userData);

        if ($answer['status'] != 200) {
            return $answer;
        }

        if (!array_key_exists($userData['id'], $this->currentUserFileslist)) {
            return [
                'body' => ERROR_MESSAGES['404'],
                'status' => 404,
            ];
        }

        $answer = $this->fileRepository->shareUserList($userData['id']);
        return [
            'body' => $answer,
            'status' => 200,
        ];
    }

    /**
     * Предоставление доступа к файлу
     *
     * @param array $userData
     * @return array
     */
    public function addShareFileUser(array $userData): array
    {
        $answer = $this->setFileList($userData);

        if ($answer['status'] != 200) {
            return $answer;
        }

        if ($answer['body']['userId'] === $userData['user_id']) {
            return [
                'body' => ERROR_MESSAGES['409'],
                'status' => 409,
            ];
        }

        $sqlUser = sprintf(
            "SELECT `email` FROM `user` WHERE `id` = %d",
            $userData['user_id']
        );
        $sqlFile = sprintf(
            "SELECT `full_path` FROM `file` WHERE `id` = %d",
            $userData['id']
        );

        if (
            !$this->fileRepository->findOne($sqlUser)
            || !$this->fileRepository->findOne($sqlFile)
        ) {
            return [
                'body' => ERROR_MESSAGES['404'],
                'status' => 404,
            ];
        }

        if ($this->findUserFileAccess($userData)) {
            return [
                'body' => 'Доступ уже предоставлен',
                'status' => 409,
            ];
        }

        return $this->fileRepository->shareFileUser([
            'fileId' => $userData['id'],
            'userId' => $userData['user_id'],
        ]);
    }

    /**
     * Прекращение доступа к файлу
     *
     * @param array $userData
     * @return array
     */
    public function unShareFileUser(array $userData): array
    {
        $answer = $this->setFileList($userData);

        if ($answer['status'] != 200) {
            return $answer;
        }

        if (!$this->findUserFileAccess($userData)) {
            return [
                'body' => ERROR_MESSAGES['404'],
                'status' => 404,
            ];
        }

        return $this->fileRepository->unshareFileUser([
            'fileId' => $userData['id'],
            'userId' => $userData['user_id'],
        ]);
    }

    /**
     * Проверка наличия записи о доступе к файлу
     *
     * @param array $userData
     * @return boolean
     */
    private function findUserFileAccess(array $userData): bool
    {
        $sql = sprintf(
            "SELECT `id` FROM `access`
            WHERE `user_id` = '%d' AND `file_id` = '%d'",
            $userData['user_id'],
            $userData['id'],
        );

        $answer = $this->fileRepository->findOne($sql);
        if (!$answer['body']) {
            return false;
        } else {
            return true;
        }
    }
}
