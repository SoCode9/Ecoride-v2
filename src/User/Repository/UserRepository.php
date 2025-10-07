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

    public function isDriver(string $userId): bool
    {
        try {
            $sql = "SELECT user_id FROM driver WHERE user_id=:userId";
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':userId', $userId, PDO::PARAM_STR);
            $statement->execute();

            return $statement->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database error in isDriver(): ($userId): " . $e->getMessage());
            throw new Exception("Impossible de savoir si l'utilisateur est un chauffeur");
        }
    }

    /**
     * Creates a new driver profile linked to a user
     * @param string $userId The UUID of the user
     * @throws \Exception If the insertion fails
     * @return void
     */
    public function createDriver(string $userId): void
    {
        try {
            $sql = 'INSERT INTO driver (user_id) VALUES (:userId)';
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':userId', $userId, PDO::PARAM_STR);
            $statement->execute();
        } catch (PDOException $e) {
            error_log("Database error in createDriver(): " . $e->getMessage());
            throw new Exception("Impossible de créer un profil conducteur");
        }
    }

    /**
     * Updates the user's role in the database
     * @param string $photoUser The path or name of the user's photo
     * @param int $roleId The new role ID to assign to the user
     * @throws \Exception If the user ID is not set or the update fails
     * @return void
     */
    public function setIdRole(string $userId, int $roleId): void
    {
        if (empty($userId)) {
            throw new Exception("Impossible de définir un rôle sans identifiant utilisateur");
        }

        try {
            $sql = "UPDATE users SET id_role = :roleId WHERE id = :userId";
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':roleId', $roleId, PDO::PARAM_INT);
            $statement->bindParam(':userId', $userId, PDO::PARAM_STR);
            $statement->execute();

            /* $this->idRole = $roleId; */
        } catch (PDOException $e) {
            error_log("Database error in setIdRole() (user ID: {$userId}) : " . $e->getMessage());
            throw new Exception("Impossible de mettre à jour le rôle de l'utilisateur");
        }
    }
    /**
     * Updates the user's profile photo in the database
     * @param string $userId The UUID of the user
     * @param string $photoUser The path or name of the user's photo
     * @throws \Exception If the user ID is not set or the update fails
     * @return void
     */
    public function setPhoto(string $userId, string $photoUser): void
    {
        if (empty($userId)) {
            throw new Exception("Impossible de modifier la photo sans identifiant utilisateur");
        }

        try {
            $sql = 'UPDATE users SET photo = :photo_user WHERE id = :user_id';
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':photo_user', $photoUser, PDO::PARAM_STR);
            $statement->bindParam(':user_id', $userId, PDO::PARAM_STR);
            $statement->execute();
        } catch (PDOException $e) {
            error_log("Database error in setPhoto() (user ID: {$userId}) : " . $e->getMessage());
            throw new Exception("Impossible de mettre à jour la photo de profil");
        }
    }
}
