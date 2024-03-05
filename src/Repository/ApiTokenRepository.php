<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;
use App\Core\Db;
use PDOException;
use App\Entity\ApiToken;

final class ApiTokenRepository extends Db
{
    public PDO $currentConnect;

    public string $apiToken;

    public function __construct()
    {
        $this->currentConnect = self::getInstance()->getConnection();
    }

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
}
