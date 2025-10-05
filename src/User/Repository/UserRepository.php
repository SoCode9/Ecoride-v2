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
            $sql = "SELECT id_role FROM users WHERE id = :userId";
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':userId', $userId, PDO::PARAM_STR);
            $statement->execute();

            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return $result['id_role'] ?? null;
        } catch (PDOException $e) {
            error_log("Database error in getRole() (idUser: {$userId}) : " . $e->getMessage());
            throw new Exception("Une erreur est survenue");
        }
    }

    /**
     * Update the user's credit in DB
     * @param string $userId connected user id
     * @param int $newCredit default = 20 credits
     * @throws \Exception
     * @return void
     */
    public function setCredit(string $userId, int $newCredit): void
    {
        if (empty($userId)) {
            throw new Exception("Impossible de mettre à jour les crédits sans identifiant utilisateur");
        }

        try {
            $sql = 'UPDATE users SET credit = :newCredit WHERE id = :idUser';
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam('newCredit', $newCredit, PDO::PARAM_INT);
            $statement->bindParam('idUser', $userId, PDO::PARAM_STR);
            $statement->execute();
        } catch (PDOException $e) {
            error_log("Database error in setCredit() (idUser: {$userId}) : " . $e->getMessage());
            throw new Exception("Impossible de mettre à jour les crédits");
        }
    }
}
