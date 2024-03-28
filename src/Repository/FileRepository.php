<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;
use App\Core\Db;
use App\Entity\File;
use PDOException;
use App\Entity\User;

final class FileRepository extends Db
{
    public PDO $currentConnect;

    public function __construct()
    {
        $this->currentConnect = self::getInstance()->getConnection();
    }

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

    // public function create(File $file): array
    // {

    //     // $sql = "INSERT INTO file (id, full_path, owner) VALUES (null, :user_id, :token, :expiresdAt)";
    //     // $state = $this->currentConnect->prepare($sql);

    //     // try {
    //     //     $state->execute(
    //     //         [
    //     //             'user_id' => $apiToken->getUser()->getId(),
    //     //             'token' => $apiToken->getToken(),
    //     //             'expiresdAt' => $apiToken->getExpiresAt()->format('Y-m-d H:i:s'),
    //     //         ]
    //     //     );
    //     // } catch (PDOException $e) {
    //     //     $state->debugDumpParams();
    //     //     return [
    //     //         'body' => $e->getMessage(),
    //     //         'status' => $e->getCode(),
    //     //     ];
    //     // }

    //     // return [
    //     //     'body' => [
    //     //         'token' => $apiToken->getToken(),
    //     //     ],
    //     //     'status' => 200,
    //     // ];
    // }

    // public function delete(File $file): array
    // {
    //     // $sql = sprintf("SELECT * FROM token WHERE user_id = '%d'", $user->getId());
    //     // $answer = $this->findOne($sql);

    //     // $ans = null;

    //     // if ($answer['body']) {
    //     //     $sql = sprintf("DELETE FROM token WHERE user_id = '%d'", $user->getId());
    //     //     $ans = $this->currentConnect->prepare($sql)->execute();
    //     // }

    //     // if ($ans) {
    //     //     $answer = [
    //     //         'body' => 'Токен успешно удалён',
    //     //         'status' => 200,
    //     //     ];
    //     // } else {
    //     //     $answer = [
    //     //         'body' => 'Токен не найден',
    //     //         'status' => 404,
    //     //     ];
    //     // }

    //     // return $answer;
    // }
}
