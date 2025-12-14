<?php

namespace App\User\Controller;

use App\Carpool\Service\CarpoolService;
use Exception;
use DateTime;

use App\Controller\BaseController;
use App\Routing\Router;

use App\User\Repository\UserRepository;
use App\Car\Repository\CarRepository;
use App\Driver\Repository\DriverRepository;
use App\Carpool\Repository\CarpoolRepository;
use App\Reservation\Repository\ReservationRepository;
use App\Rating\Repository\RatingRepository;

use App\User\Service\UserService;
use App\Driver\Service\DriverService;

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

        $userId = $_SESSION['user_id'] ?? null;
        $user = $this->repo->findById($userId);

        $driRepo = new DriverRepository($this->repo);
        $driver = $driRepo->makeFromUserId($userId);

        $formattedUser = $this->service->displayProfil($user);

        $car = new CarRepository();
        $cars = $car->findAllCars($userId);

        $brands = $car->findAllBrands();

        return $this->render('pages/user_space/profile.php', 'Mon espace', [
            'user' => $user,
            'formattedUser' => $formattedUser,
            'driver' => $driver,
            'cars' => $cars,
            'brands' => $brands
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

    public function editPhoto()
    {

        $userId = $_SESSION['user_id'];

        try {
            // Check if a file was sent and if there were no upload errors
            if (!isset($_FILES['new_photo']) || $_FILES['new_photo']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Erreur lors du téléchargement du fichier");
            }
            $file = $_FILES['new_photo'];

            // process the photo
            $uniqueName = $this->service->editPhoto($userId, $file);

            // Update the user's photo path in the database
            $this->repo->setPhoto($userId, $uniqueName);
            $_SESSION['success_message'] = "Votre photo a été mise à jour avec succès";
        } catch (Exception $e) {
            error_log("Erreur upload photo (user ID $userId) : " . $e->getMessage());
            $_SESSION['error_message'] = $e->getMessage();
        }

        header('Location: ' . BASE_URL . '/mon-profil');
        exit;
    }

    public function listCarpools()
    {
        $userId = $_SESSION['user_id'];

        $car = new CarRepository();
        $cars = $car->findAllCars($userId);

        if ($this->repo->isDriver($userId) && $cars !== []) {
            $carpoolButton = '<a class="btn action-btn" style="padding: 8px; text-align:right;"
                href=' . BASE_URL . '/mes-covoiturages/nouveau>Proposer un covoiturage</a>';
        }

        $userRepo = new UserRepository();
        $driverService = new DriverService(new DriverRepository($userRepo));
        $carpoolRepo = new CarpoolRepository();
        $carpoolSer = new CarpoolService(
            $carpoolRepo,
            $driverService,
            new ReservationRepository(),
            new CarRepository(),
            new UserRepository(),
            $this->router
        );

        $now = new DateTime();

        $carpoolListToValidate = $carpoolSer->listCarpoolToValidate($userId);
        $carpoolListNotStarted = $carpoolSer->listCarpoolNotStarted($userId);
        $carpoolListCompleted = $carpoolSer->listCarpoolCompleted($userId);

        return $this->render('pages/user_space/carpools.php', 'Mon espace', [
            'carpoolButton' => $carpoolButton ?? null,
            'carpoolListToValidate' => $carpoolListToValidate,
            'carpoolListNotStarted' => $carpoolListNotStarted,
            'carpoolListCompleted' => $carpoolListCompleted,
            'now' => $now
        ]);
    }

    public function employeeValidateRatings()
    {
        $userId = $_SESSION['user_id'] ?? null;
        $user = $this->repo->findById($userId);

        //VERIFY RATINGS TAB

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $ratingsPerPage = 4;
        $offset = ($page - 1) * $ratingsPerPage;

        $ratingRepo = new RatingRepository();
        $ratingsInValidation = $ratingRepo->loadRatingsInValidation($ratingsPerPage, $offset);

        // pagination
        $totalRatings = $ratingRepo->countAllRatingsInValidation();

        $totalPages = ceil($totalRatings / $ratingsPerPage);

        return $this->render('pages/employee_space/validate_ratings.php', 'Espace Employé', [
            'user' => $user,
            'ratingsInValidation' => $ratingsInValidation,
            'totalPages' => $totalPages,
            'page' => $page,
            'totalRatings' => $totalRatings
        ]);
    }

    public function employeeBadComments()
    {
        $userId = $_SESSION['user_id'] ?? null;
        $user = $this->repo->findById($userId);



        return $this->render('pages/employee_space/bad_comments.php', 'Espace Employé', [
            'user' => $user
        ]);
    }
}
