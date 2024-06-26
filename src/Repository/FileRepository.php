<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;
use App\Core\Db;
use App\Entity\File;
use PDOException;

final class FileRepository extends Db
{
    public PDO $currentConnect;

    public function __construct()
    {
        $this->currentConnect = self::getInstance()->getConnection();
    }

    /**
     * Изменение данных о файле в БД
     *
     * @param File $file
     * @param string $newFileName
     * @return array
     */
    public function rename(File $file, string $newFileName): array
    {
        $sql = sprintf(
            "UPDATE `file` SET `full_path` = '/%s' WHERE `id`= %d",
            $newFileName,
            $file->getId()
        );

        $state = $this->currentConnect->prepare($sql);
        $answer = $state->execute();

        if ($answer) {
            return [
                'body' => 'Данные успешно изменены',
                'status' => 200,
            ];
        } else {
            return [
                'body' => 'Ошибка доступа к БД',
                'status' => 403,
            ];
        }
    }

    /**
     * Загрузка файла
     *
     * @param array $fileData
     * @return array
     */
    public function addFile(array $fileData): array
    {
        $answer = null;
        try {
            $state = $this->currentConnect->beginTransaction();

            $sql = sprintf(
                "INSERT INTO `file`(`id`, `full_path`) VALUES (null, '%s')",
                $fileData['fullPath']
            );
            $state = $this->currentConnect->prepare($sql);
            $answer = $state->execute();
            $fileId = 0;
            if (!$answer) {
                throw new PDOException("Ошибка боступа к БД");
            }
            $fileId = $this->currentConnect->lastInsertId();
            $sql = sprintf(
                "INSERT INTO `access`(`id`, `user_id`, `file_id`)
                VALUES (null, '%s', '%s')",
                $fileData['userId'],
                $fileId
            );
            $state = $this->currentConnect->prepare($sql);
            $answer = $state->execute();
        } catch (PDOException $e) {
            $this->currentConnect->rollBack();
            return [
                'body' => $e->getMessage(),
                'status' => 409,
            ];
        }
        $answer = $this->currentConnect->commit();

        return [
            'body' => 'Файл загружен',
            'status' => 200,
        ];
    }

    /**
     * Удаление файла
     *
     * @param File $file
     * @return array
     */
    public function remove(File $file): array
    {
        $sql = sprintf("DELETE FROM `file` WHERE id = '%d'", $file->getId());
        return $this->delete($sql);
    }

    /**
     * Список пользователей, имеющих доступ к файлу
     *
     * @param integer $fileId
     * @return array
     */
    public function shareUserList(int $fileId): array
    {
        $sql = sprintf(
            "SELECT `user`.`id`, `user`.`email`, `user`.`name`, `user`.`surname`
            FROM `access`
            JOIN `user` ON `user`.`id`= `access`.`user_id`
            WHERE `file_id` = %d",
            $fileId
        );

        return $this->findAll($sql);
    }

    /**
     * Предоставление доступа к файлу
     *
     * @param array $fileData
     * @return array
     */
    public function shareFileUser(array $fileData): array
    {
        $sql = sprintf(
            "INSERT INTO `access`(`id`, `user_id`, `file_id`)
            VALUES (null, '%s', '%s')",
            $fileData['userId'],
            $fileData['fileId']
        );
        $state = $this->currentConnect->prepare($sql);
        $answer = $state->execute();

        if ($answer) {
            return [
                'body' => 'Доступ к файлу открыт',
                'status' => 200,
            ];
        } else {
            return [
                'body' => 'Ошибка доступа к БД',
                'status' => 409,
            ];
        }
    }

    /**
     * Прекращение доступа к файлу
     *
     * @param array $fileData
     * @return array
     */
    public function unshareFileUser(array $fileData): array
    {
        $sql = sprintf(
            "DELETE FROM `access`
            WHERE `user_id` = '%d' AND `file_id` = '%d'",
            $fileData['userId'],
            $fileData['fileId']
        );
        $answer = $this->delete($sql);

        if ($answer) {
            return [
                'body' => 'Доступ к файлу отменён',
                'status' => 200,
            ];
        } else {
            return [
                'body' => 'Ошибка доступа к БД',
                'status' => 409,
            ];
        }
    }
}
