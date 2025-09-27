<?php

namespace App\User\Repository;

use App\Database\DbConnection;
use PDO;

use App\Controller\BaseController;
use App\Utils\Formatting\DateFormatter;
use DateTime;
use PDOException;
use Exception;

class UserRepository extends BaseController
{

    public function getRole($userId): int
    {
        try {
            $sql = "SELECT id FROM users WHERE id = :userId";
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':userId', $userId, PDO::PARAM_STR);
            $statement->execute();

            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return $result['id'] ?? null;
        } catch (PDOException $e) {
            error_log("Database error in getRole() (idUser: {$userId}) : " . $e->getMessage());
            throw new Exception("Une erreur est survenue");
        }
    }
}
