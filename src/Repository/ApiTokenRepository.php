<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;
use App\Core\Db;
use PDOException;
use App\Entity\User;
use App\Entity\ApiToken;

final class ApiTokenRepository extends Db
{
    public PDO $currentConnect;

    public string $apiToken;

    public function __construct()
    {
        $this->currentConnect = self::getInstance()->getConnection();
    }

    /**
     * @param ApiToken $apiToken
     * @return array
     */
    public function create(ApiToken $apiToken): array
    {

        $sql = "INSERT INTO token (id, user_id, token, expiresAt) VALUES (null, :user_id, :token, :expiresdAt)";
        $state = $this->currentConnect->prepare($sql);

        try {
            $state->execute(
                [
                    'user_id' => $apiToken->getUser()->getId(),
                    'token' => $apiToken->getToken(),
                    'expiresdAt' => $apiToken->getExpiresAt()->format('Y-m-d H:i:s'),
                ]
            );
        } catch (PDOException $e) {
            $state->debugDumpParams();
            return [
                'body' => $e->getMessage(),
                'status' => $e->getCode(),
            ];
        }

        return [
            'body' => [
                'token' => $apiToken->getToken(),
            ],
            'status' => 200,
        ];
    }

    /**
     * @param array $userData
     * @return array
     */
    public function logout(array $userData): array
    {
        $sql = sprintf("SELECT * FROM token WHERE token = '%s'", $userData['token']);
        $answer = $this->findOne($sql);

        if (!$answer['body']) {
            return [
                'body' => 'Ошибка чтения токена',
                'status' => $answer['status'],
            ];
        }

        $sql = sprintf("DELETE FROM token WHERE token = '%s'", $userData['token']);
        $state = $this->currentConnect->prepare($sql);
        $state->execute();

        return [
            'body' => 'Выход из системы выполнен',
            'status' => 200,
        ];
    }

    /**
     * @param User $user
     * @return array
     */
    public function deleteToken(User $user): array
    {
        $sql = sprintf("SELECT * FROM token WHERE user_id = '%d'", $user->getId());
        $answer = $this->findOne($sql);

        $ans = null;

        if ($answer['body']) {
            $sql = sprintf("DELETE FROM token WHERE user_id = '%d'", $user->getId());
            $ans = $this->currentConnect->prepare($sql)->execute();
        }

        if ($ans) {
            $answer = [
                'body' => 'Токен успешно удалён',
                'status' => 200,
            ];
        } else {
            $answer = [
                'body' => 'Токен не найден',
                'status' => 404,
            ];
        }

        return $answer;
    }
}
