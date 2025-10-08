<?php

namespace App\Reservation\Service;

use App\Database\DbConnection;
use Exception;

use App\Reservation\Repository\ReservationRepository;

use App\User\Repository\UserRepository;
use App\Carpool\Repository\CarpoolRepository;
use App\Car\Repository\CarRepository;

use App\Routing\Router;
use App\Utils\Formatting\DateFormatter;
use App\Utils\Formatting\OtherFormatter;

final class ReservationService
{
    public function __construct(
        private ReservationRepository $repo,
        private UserRepository $userRepo,
        private CarpoolRepository $carpoolRepo,
        private CarRepository $carRepo
    ) {}


    public function checkParticipation()
    {
        try {
            // Check if carpool ID is sent
            if (!isset($_POST['carpool_id'])) {
                throw new Exception("ID du covoiturage manquant");
            }
            $carpoolId = $_POST['carpool_id'];
            $userId = $_SESSION['user_id'] ?? null;

            // Check that the user is logged in
            if (!isset($userId)) {
                throw new Exception("Utilisateur non connecte");
            }

            // Check if user is already a passenger
            $reservation = $this->repo;
            if ($reservation->existsForUserAndCarpool($userId, $carpoolId)) {
                throw new Exception("Utilisateur déjà inscrit à ce covoiturage");
            }

            // Retrieve user's credits
            $user = $this->userRepo->findById($userId);
            $userCredit = (int)$user->getCredit();

            // Check the available seats and retrieve the carpool's price
            $seatsAllocated = (int)$this->repo->countPassengers($carpoolId);

            $carpool = $this->carpoolRepo->findById($carpoolId);
            $seatsOffered = $this->carRepo->getSeatsOfferedByCar($carpool->getCarId());

            //$seatsOffered = (int)$car->getSeatsOfferedByCar($carpool->getCarId());
            $availableSeats = max(0, $seatsOffered - $seatsAllocated);

            $carpoolPrice = (int)$carpool->getPrice();

            // Check the carpool's status
            if ($carpool->getStatus() !== 'not started') {
                throw new Exception("Le covoiturage est soit en cours, soit annulé, soit terminé");
            }
            echo json_encode([
                "success" => true,
                "availableSeats" => $availableSeats,
                "userCredits" => $userCredit,
                "travelPrice" => $carpoolPrice
            ]);
        } catch (Exception $e) {
            error_log("ReservationService - Error in checkParticipation() : " . $e->getMessage());
            echo json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function updateParticipation()
    {
        $pdo = DbConnection::getPdo();
        try {
            $pdo->beginTransaction();

            ### second check ###
            $userId = $_SESSION['user_id'];

            $carpoolId = $_POST['carpool_id'] ?? null;

            // Check if carpool ID is sent
            if (!isset($carpoolId)) {
                throw new Exception("ID du covoiturage manquant");
            }

            // Check the available seats and retrieve the carpool's price
            $reservation = $this->repo;
            $carpool = $this->carpoolRepo->findById($carpoolId);
            $seatsOffered = $this->carRepo->getSeatsOfferedByCar($carpool->getCarId());

            $seatsAllocated = (int)$reservation->countPassengers($carpoolId);
            $availableSeats = max(0, $seatsOffered - $seatsAllocated);
            if ($availableSeats = 0) {
                throw new Exception("Plus de places disponibles");
            }
            $travelPrice = (int)$carpool->getPrice();

            // Check the carpool's status
            if ($carpool->getStatus() !== 'not started') {
                throw new Exception("Impossible de participer à ce covoiturage");
            }

            // Check that the user has enough credits 
            $user = $this->userRepo->findById($userId);
            $userCredit = (int)$user->getCredit();

            if ($userCredit < $travelPrice) {
                throw new Exception("Crédits insuffisants");
            }

            ### END second check ###

            // UPDATE DataBase 

            //debit the user
            $this->userRepo->setCredit($userId, -$travelPrice);

            //create the reservation in DB
            $this->repo->new($userId, $carpoolId, $travelPrice);

            $pdo->commit();
            echo json_encode(["success" => true, "message" => "Participation confirmée !"]);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("Error in update_participation.php : " . $e->getMessage());
            echo json_encode([
                "success" => false,
                "message" => "Une erreur est survenue"
            ]);
        }
    }

    /**
     * When a carpool is approved (YES)
     * @param int $reservationId
     * @throws \Exception
     * @return void
     */
    public function carpoolApproved(int $reservationId): void
    {
        try {
            $pdo = DbConnection::getPdo();
            $pdo->beginTransaction();

            $creditSpent = $this->repo->getCreditSpent($reservationId);

            $driverId = $this->repo->getDriverIdFromReservation($reservationId);
            $this->userRepo->setCredit($driverId, $creditSpent);

            $this->repo->setValidated($reservationId);

            $carpoolId = $this->repo->getCarpoolIdFromReservation($reservationId);
            $notValidated = $this->repo->getReservationsNotValidatedOfACarpool($carpoolId);

            if (empty($notValidated)) {
                $carpoolRepo = new CarpoolRepository();

                $carpoolRepo->setCarpoolStatus('ended', $carpoolId);

                $this->userRepo->setCredit($driverId, -2);
            }

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Error in carpoolApproved() : " . $e->getMessage());
            throw new Exception("Impossible de valider la réservation");
        }
    }

    /**
     * When a carpool is rejected (NO)
     * @param int $reservationId
     * @param string $badComment
     * @return void
     */
    public function carpoolRejected(int $reservationId, string $badComment): void
    {
        $this->repo->addBadComment($reservationId, $badComment);
        $this->repo->setValidated($reservationId);
    }
}
