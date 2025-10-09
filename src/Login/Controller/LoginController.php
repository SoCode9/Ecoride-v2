<?php

namespace App\Login\Controller;

use App\Controller\BaseController;
use App\Routing\Router;
use Exception;

use App\Login\Repository\LoginRepository;
use App\Login\Service\LoginService;

class LoginController extends BaseController
{

    private LoginService $service;
    private LoginRepository $repo;

    public function __construct(Router $router)
    {
        parent::__construct($router);
        $this->repo = new LoginRepository();
        $this->service = new LoginService($this->repo);
    }

    public function loginPage()
    {
        return $this->render('pages/login/login.php', 'Connection', []);
    }

    public function login()
    {
        try {
            $mail = $_POST['mail'];
            $password = $_POST['password'];

            $searchUser = $this->repo->checkCredentials($mail, $password);

            $_SESSION['success_message'] = 'Connexion réussie !';
            $_SESSION['user_id'] = $searchUser->getId();
            $_SESSION['role_user'] = $searchUser->getIdRole();
            // token creation
            if (empty($_SESSION['csrf'])) {
                $_SESSION['csrf'] = bin2hex(random_bytes(32));
            }
            header('Location: ' . BASE_URL . '/');
            exit();
        } catch (Exception $e) {
            error_log("Error in the connection process : " . $e->getMessage());
            $_SESSION['error_message'] = "Une erreur est survenue";
            header('Location:' . BASE_URL . '/connection');
            exit();
        }
    }

    public function logout()
    {
        $_SESSION = [];
        session_destroy();
        $_SESSION['success_message'] = 'Déconnexion réussie !';

        return $this->loginPage();
    }

    public function newAccount()
    {
        try {
            $pseudo = $_POST['pseudo'];
            $mail = $_POST['mail'];
            $password = $_POST['password'];

            // User creation
            $newUser = $this->service->register($pseudo, $mail, $password, 1);

            // Check if user is created well
            $_SESSION['success_message'] = 'Compte créé avec succès ! Vous avez été crédité de 20 crédits 🎉';
            $_SESSION['user_id'] = $newUser->getId();
            $_SESSION['role_user'] = $newUser->getIdRole();
            // token creation
            if (empty($_SESSION['csrf'])) {
                $_SESSION['csrf'] = bin2hex(random_bytes(32));
            }
            header('Location:' . BASE_URL . '/');
            exit();
        } catch (Exception $e) {
            error_log("Error in newAccount() : " . $e->getMessage());
            $_SESSION['error_message'] = "Une erreur est survenue";
            header('Location:' . BASE_URL . '/connection');
            exit();
        }
    }
}
