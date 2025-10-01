<?php

namespace App\User\Repository;

use App\Database\DbConnection;
use PDO;
use PDOException;
use Exception;

use App\User\Entity\User;

class UserRepository
{


    public function findById(string $id): ?User
    {
        try {
            $sql = "SELECT * FROM users WHERE id = :id";
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':id', $id, PDO::PARAM_STR);
            $statement->execute();
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            if (!$row) return null;

            return new User(
                $row['id'],
                $row['pseudo'],
                $row['mail'],
                $row['password'],
                $row['credit'],
                $row['photo'],
                $row['id_role'],
                $row['is_activated'],
            );
        } catch (PDOException $e) {
            error_log("UserRepository - Database error in findById() : " . $e->getMessage());
            throw new Exception("Une erreur est survenue");
        }
    }

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
