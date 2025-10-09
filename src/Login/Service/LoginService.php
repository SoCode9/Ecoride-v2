<?php

namespace App\Login\Service;

use Exception;

use App\Driver\Repository\DriverRepository;
use App\Driver\Service\DriverService;
use App\User\Entity\User;


use App\Login\Repository\LoginRepository;

use App\Utils\Formatting\OtherFormatter;

final class LoginService
{
    public function __construct(private LoginRepository $repo) {}

    /**
     * Register a new user and store it in the databaser
     * @param string $pseudo The user's username
     * @param string $mail The user's email address
     * @param string $password The plain text password to be hashed
     * @param int $roleId The role ID to assign to the user
     * @return
     */
    public function register(string $pseudo, string $mail, string $password, int $roleId)
    {

        $this->validatePasswordStrength($password);
        $password = password_hash($password, PASSWORD_BCRYPT);

        $user = $this->repo->newUser($pseudo,  $mail,  $password,  $roleId);

        return $user;
    }

    /**
     * Validates the strength of the user's password.
     * A valid password must be at least 8 characters long,
     * and contain at least one uppercase letter, one lowercase letter,
     * one digit, and one special character.
     * @param string $password The plain text password to validate
     * @throws \Exception If the password is too weak
     * @return void 
     */
    public function validatePasswordStrength(string $password): void
    {
        if (strlen($password) < 8) {
            throw new Exception("Le mot de passe est trop court (minimum 8 caractères)");
        }

        if (
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/[0-9]/', $password) ||
            !preg_match('/[\W]/', $password)
        ) {
            throw new Exception("Le mot de passe doit contenir au moins 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial");
        }
    }
}
