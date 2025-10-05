<?php

namespace App\User\Controller;

use Exception;

use App\Controller\BaseController;
use App\Routing\Router;

use App\User\Repository\UserRepository;
use App\Car\Repository\CarRepository;
use App\Driver\Repository\DriverRepository;
use App\User\Service\UserService;

class UserController extends BaseController
{

    private UserService $service;
    private UserRepository $repo;

    public function __construct(Router $router)
    {
        parent::__construct($router);
        $this->repo = new UserRepository();
        $this->service = new UserService($this->repo);
    }

    public function profile()
    {

        $userId  = $_SESSION['user_id'] ?? null;
        $user = $this->repo->findById($userId);

        $driRepo = new DriverRepository($this->repo);
        $driver =  $driRepo->makeFromUserId($userId);

        $formattedUser = $this->service->displayProfil($user);

        $car = new CarRepository();
        $cars = $car->findAllCars($userId);

        return $this->render('pages/user_space/profile.php', 'Mon espace', [
            'user' => $user,
            'formattedUser' => $formattedUser,
            'driver' => $driver,
            'cars' => $cars
        ]);
    }

    public function editProfile()
    {

        // check if role ID is sent
        if (!isset($_POST['role_id'])) {
            echo json_encode(["success" => false, "message" => "ID du rôle manquant"]);
            exit;
        }

        $roleId = (int) $_POST['role_id'];
        $userId = $_SESSION['user_id'] ?? null;

        if ($roleId === null || !in_array($roleId, [1, 2, 3])) {
            echo json_encode(["success" => false, "message" => "Rôle invalide"]);
            exit;
        }

        $smokePref = $this->processPreference($_POST['smoke_pref'] ?? null, "fumeur");
        $petPref = $this->processPreference($_POST['pet_pref'] ?? null, "animaux");
        $foodPref = $this->processPreference($_POST['food_pref'] ?? null, "nourriture");
        $speakPref = $this->processPreference($_POST['speak_pref'] ?? null, "discussion");
        $musicPref = $this->processPreference($_POST['music_pref'] ?? null, "musique");

        try {
            if (($this->repo->isDriver($userId)) === false) {
                $this->repo->createDriver($userId);
            }

            $driRepo = new DriverRepository($this->repo);

            $this->repo->setIdRole($userId, $roleId);
            $driRepo->updateDriverPreference($userId, 'smoker', $smokePref);
            $driRepo->updateDriverPreference($userId, 'pets', $petPref);
            $driRepo->updateDriverPreference($userId, 'food', $foodPref);
            $driRepo->updateDriverPreference($userId, 'speaker', $speakPref);
            $driRepo->updateDriverPreference($userId, 'music', $musicPref);

            $driRepo->makeFromUserId($userId);


            $_SESSION['role_user'] = $roleId;
            echo json_encode(["success" => true, "message" => "Rôle et préférences mis à jour"]);
            $_SESSION['success_message'] = "Profil mis à jour";
            exit;
        } catch (Exception $e) {
            error_log("Erreur editProfile (user ID: $userId) : " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Impossible de mettre à jour votre profil"]);
        }
    }

    private function processPreference($preference, $preferenceName)
    {
        if ($preference === "NULL") {
            return null; // Convert “NULL” to true NULL for SQL
        } elseif ($preference === "0" || $preference === "1") {
            return (int) $preference; // Convert in int
        } else {
            echo json_encode(["success" => false, "message" => "Préférence $preferenceName invalide"]);
            exit;
        }
    }
}
