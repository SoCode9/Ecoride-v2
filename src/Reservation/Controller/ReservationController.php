<?php

namespace App\Reservation\Controller;

use App\Controller\BaseController;
use App\Routing\Router;
use Exception;

use App\Reservation\Repository\ReservationRepository;
use App\User\Repository\UserRepository;
use App\Carpool\Repository\CarpoolRepository;
use App\Car\Repository\CarRepository;
use App\Rating\Repository\RatingRepository;

use App\Reservation\Service\ReservationService;

use App\Utils\Formatting\DateFormatter;
use App\Utils\MailService;

class ReservationController extends BaseController
{
    private ReservationService $service;
    private ReservationRepository $repo;

    public function __construct(Router $router)
    {
        $userRepo = new UserRepository();
        $carpoolRepo = new CarpoolRepository();
        $carRepo = new CarRepository();
        $this->repo = new ReservationRepository();

        $this->service = new ReservationService($this->repo, $userRepo, $carpoolRepo, $carRepo, new MailService());
    }

    public function checkParticipation()
    {

        $this->service->checkParticipation();
    }

    public function updateParticipation()
    {

        $this->service->updateParticipation();
    }

    public function carpoolApproved()
    {

        try {

            $userId = $_SESSION['user_id'];

            $reservationId = $_POST['idReservation'];
            $rating = $_POST['rating'];
            if ($rating === '') {
                $rating = null;
            }
            $comment = $_POST['comment'] ?? null;

            $driverId = $this->repo->getDriverIdFromReservation($reservationId);

            if (isset($rating)) {
                $newRating = new RatingRepository();
                $newRating->new($userId, $driverId, $rating, $comment);
            }

            $this->service->carpoolApproved($reservationId);

            header('Location:' . BASE_URL . '/mes-covoiturages');
            $_SESSION['success_message'] = "Le covoiturage a été validé";
        } catch (Exception $e) {
            error_log("Carpool validation error : " . $e->getMessage());
            header('Location:' . BASE_URL . '/mes-covoiturages');
            $_SESSION['error_message'] = "Une erreur est survenue";
            exit;
        }
    }

    public function carpoolRejected()
    {
        try {
            $reservationId = $_POST['idReservation'];
            $comment = $_POST['comment'];

            $this->service->carpoolRejected($reservationId, $comment);
            header('Location:' . BASE_URL . '/mes-covoiturages');
            $_SESSION['success_message'] = "Votre retour a été transmis pour traitement";
        } catch (Exception $e) {
            error_log("Carpool validation error : " . $e->getMessage());
            header('Location:' . BASE_URL . '/mes-covoiturages');
            $_SESSION['error_message'] = "Une erreur est survenue";
            exit;
        }
    }

    public function cancelCarpool(): void
    {
        $userId    = $_SESSION['user_id'] ?? null;
        $carpoolId = (string)($_GET['id'] ?? '');

        if (!$userId || $carpoolId === '') {
            $_SESSION['error_message'] = "ID du covoiturage manquant";
            header('Location:' . BASE_URL . '/mes-covoiturages');
            return;
        }

        try {
            $message = $this->service->cancelByCurrentUser($userId, $carpoolId);
            $_SESSION['success_message'] = $message;
        } catch (\Throwable $e) {
            error_log("Error in cancel a carpool : " . $e->getMessage());
            $_SESSION['error_message'] = "Impossible d'annuler le covoiturage.";
        }

        header('Location:' . BASE_URL . '/mes-covoiturages');
    }

    public function startCarpool(): void
    {
        $carpoolId = (string)($_GET['id'] ?? '');

        try {
            $carpoolRepo = new CarpoolRepository();
            $carpoolRepo->setCarpoolStatus('in progress', $carpoolId);
            header('Location:' . BASE_URL . '/mes-covoiturages');
            $_SESSION['success_message'] = "Le covoiturage a débuté.";
        } catch (Exception $e) {
            error_log("Error in start a carpool : " . $e->getMessage());
            $_SESSION['error_message'] = "Une erreur est survenue";
            header('Location:' . BASE_URL . '/mes-covoiturages');
        }
    }

    public function completedCarpool(): void
    {
        $carpoolId = (string)($_GET['id'] ?? '');

        try {
            $message = $this->service->completedCarpool($carpoolId);
            $_SESSION['success_message'] = $message;
        } catch (\Throwable $e) {
            error_log("Error in complete a carpool : " . $e->getMessage());
            $_SESSION['error_message'] = "Une erreur est survenue";
        }

        header('Location:' . BASE_URL . '/mes-covoiturages');
    }
}
