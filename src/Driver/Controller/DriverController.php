<?php

namespace App\Driver\Controller;

use App\Controller\BaseController;
use App\Routing\Router;
use PDOException;
use Exception;

use App\Utils\Formatting\DateFormatter;

use App\Driver\Repository\DriverRepository;
use App\User\Repository\UserRepository;
use App\Driver\Service\DriverService;

class DriverController extends BaseController
{

    private DriverService $service;
    private DriverRepository $repo;

    public function __construct(Router $router)
    {
        parent::__construct($router);
        $this->repo = new DriverRepository(new UserRepository());
        $this->service = new DriverService($this->repo);
    }

    public function newOtherPreference()
    {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            $newPrefInsert = $_POST['new_pref'];

            $this->repo->newCustomPreference($userId, $newPrefInsert);
            echo json_encode([
                "success" => true,
                "newPrefInsert" => $newPrefInsert
            ]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "error" => $e->getMessage()]);
        }
    }

    public function listOtherPreferences()
    {
        $userId = $_SESSION['user_id'] ?? null;

        $driver = $this->repo->makeFromUserId($userId);

        return $this->renderPartial('components/otherPreferences/list.php', [
            'driver' => $driver
        ]);
    }

    public function updateOtherPreference()
    {
        $idPrefToUpdate = $_POST['id'];
        $newCustomPref = $_POST['newCustomPref'];

        try {
            $this->repo->updateCustomPreference($idPrefToUpdate, $newCustomPref);
            header('Location: ' . BASE_URL . '/mon-profil');
            $_SESSION['success_message'] = "La préférence a été modifiée";

            exit;
        } catch (Exception $e) {
            error_log("DriverController - Database error in updateOtherPreference(): " . $e->getMessage());
            header('Location: ' . BASE_URL . '/mon-profil');
            $_SESSION['error_message'] = "Erreur lors de la modification de la préférence";
            exit;
        }
    }

    public function deleteOtherPreference()
    {
        $idPrefToDelete = $_POST['id']; 
        try {
            $this->repo->deleteCustomPreference($idPrefToDelete);

            header('Location: ' . BASE_URL . '/mon-profil');
            $_SESSION['success_message'] = "La préférence a été supprimée";

            exit;
        } catch (Exception $e) {
            error_log("DriverController - Database error in deleteOtherPreference(): " . $e->getMessage());
            header('Location: ' . BASE_URL . '/mon-profil');
            $_SESSION['error_message'] = "Erreur lors de la suppression de la préférence";
            exit;
        }
    }
}
