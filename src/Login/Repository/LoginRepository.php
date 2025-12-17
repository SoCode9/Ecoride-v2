<?php

namespace App\Login\Repository;

use App\Database\DbConnection;
use App\User\Repository\UserRepository;
use PDO;
use PDOException;
use Exception;

use App\User\Entity\User;

class LoginRepository
{

    /**
     * Authenticate a user by verifying the email and password.
     * Initializes the object if credentials are valid and the account is active.
     * @param string $mailTested The email address provided by the user
     * @param string $passwordTested The plain password provided by the user
     * @throws \Exception If authentication fails (invalid credentials or inactive account)
     * @return User
     */
    public function checkCredentials(string $mailTested, string $passwordTested): User
    {
        $sql = 'SELECT * FROM users WHERE (mail=:mailTested)';
        $pdo = DbConnection::getPdo();
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':mailTested', $mailTested, PDO::PARAM_STR);
        $statement->execute();

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        if (
            ($user === false)
            || ($user['is_activated'] === 0)
            || (password_verify($passwordTested, $user['password']) === false)
            || (!isset($user['id']))
        ) {
            throw new Exception("Identifiants invalides");
        }

        return new User(
            $user['id'],
            $user['pseudo'],
            $user['mail'],
            $user['password'],
            $user['credit'],
            $user['photo'],
            $user['id_role'],
            $user['is_activated'],
        );
    }

    /**
     * Creates a new user in the database. Can be a regular user or an employee
     * @param string $pseudo
     * @param string $mail
     * @param string $password
     * @param int $roleId Role ID: 1 for self-registered users (passenger), 4 for admin-created employees
     * @throws \Exception If the user already exists or a database error occurs.
     * @return User|null
     */
    public function newUser(string $pseudo, string $mail, string $password, int $roleId): User|null
    {
        $userId = $this->findUserByMail($mail);

        if ($userId !== null) {
            error_log("Account already exists for this email address: {$mail}");
            throw new Exception("Une erreur est survenue");
        }

        try {
            $statement = DbConnection::getPdo()->prepare("INSERT INTO users (id, pseudo, mail, password, id_role, credit) VALUES (UUID(), :pseudo, :mail, :password, :idRole, 0)");
            $statement->execute([
                ':pseudo' => $pseudo,
                ':mail' => $mail,
                ':password' => $password,
                ':idRole' => $roleId,
            ]);


            $userId = $this->findUserByMail($mail);
            $userRepo = new UserRepository();

            if ($roleId === 1) {
                $userRepo->setCredit($userId, 20);
            }
            $user = $userRepo->findById($userId);

            return $user;
        } catch (PDOException $e) {
            error_log("LoginRepository - Database error in newUser(): " . $e->getMessage());
            throw new Exception("Une erreur est survenue");
        }
    }

    /**
     * Retrieves the user ID from the database using the stored email address.
     * @throws \Exception If the email is not set or if the query fails.
     * @return string|null The user's ID if found, or null if not found.
     */
    private function findUserByMail(string $mail): mixed
    {
        if (empty($mail)) {
            throw new Exception("Aucune adresse email envoyée pour récupérer l'ID utilisateur");
        }

        try {
            $sql = "SELECT id FROM users WHERE mail = :mail";
            $pdo = DbConnection::getPdo();
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':mail', $mail, PDO::PARAM_STR);
            $statement->execute();

            $result = $statement->fetch(PDO::FETCH_ASSOC);
            $id = $result['id'] ?? null;

            return $id;
        } catch (PDOException $e) {
            error_log("LoginRepository - Database error in findUserByMail() (mail: {$mail}) : " . $e->getMessage());
            throw new Exception("Impossible de récupérer l'identifiant de l'utilisateur");
        }
    }
}
