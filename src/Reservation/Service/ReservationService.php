<?php

namespace App\Reservation\Service;

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
            // Check if travel ID is sent
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

           // var_dump($availableSeats);
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
}
