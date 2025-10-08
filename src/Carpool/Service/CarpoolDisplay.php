<?php

namespace App\Carpool\Service;

use App\Carpool\Entity\Carpool;
use App\Car\Service\CarService;
use App\Driver\Service\DriverService;
use App\Routing\Router;
use App\Reservation\Repository\ReservationRepository;
use App\Car\Repository\CarRepository;
use App\User\Repository\UserRepository;
use App\Utils\Formatting\OtherFormatter;
use App\Utils\Formatting\DateFormatter;
use DateTime;

final class CarpoolDisplay
{


    /**
     * @param Carpool|array $c  Entité (détail) OU row SQL (liste)
     */
    public static function one(
        Carpool|array $c,
        ?string $currentUserId,
        DriverService $driverService,
        CarpoolService $carpoolService,
        UserRepository $userRepo,
        Router $router,
        bool $withActions = false
    ): array {

        if (gettype($c) === 'object') {
            $userRepo = new UserRepository();
            $carRepo = new CarRepository();
            $dataUser = $userRepo->findById($c->getIdDriver());
            $dataCar = $carRepo->findById($c->getCarId());
        }

        // --- accès unifiés (entité vs row) ---
        $id            = is_array($c) ? (string)($c['id'] ?? '')             : (string)$c->getIdCarpool();
        $driverId      = is_array($c) ? (string)($c['driver_id'] ?? '')      : (string)$c->getIdDriver();
        $reservationId = is_array($c) ? (string)($c['reservationId'] ?? '')  : null;
        $driverpseudo  = is_array($c) ? (string)($c['pseudo'] ?? '')         : (string)$dataUser->getPseudo();
        $depCity    = is_array($c) ? ($c['departure_city'] ?? null)       : $c->getDepartureCity();
        $arrCity    = is_array($c) ? ($c['arrival_city'] ?? null)         : $c->getArrivalCity();
        $depTimeRaw    = is_array($c) ? ($c['departure_time'] ?? null)       : $c->getDepartureTime();
        $arrTimeRaw    = is_array($c) ? ($c['arrival_time'] ?? null)         : $c->getArrivalTime();
        $dateRaw       = is_array($c) ? ($c['date'] ?? null)                 : $c->getDate();
        $status        = is_array($c) ? (string)($c['status'] ?? '')         : (string)$c->getStatus();
        $carId         = is_array($c) ? (int)($c['car_id'] ?? 0)             : (int)$c->getCarId();
        $price         = is_array($c) ? (int)($c['price'] ?? 0)              : (int)$c->getPrice();
        $driverPhoto   = is_array($c) ? ($c['driver_photo'] ?? null)         : $dataUser->getPhoto();
        $carElectric   = is_array($c) ? (bool)($c['electric'] ?? 0)      : $dataCar->isElectric();

        // --- dérivés partagés ---
        $isOwner = $currentUserId && $driverId !== '' && $driverId === (string)$currentUserId;
        $avg     = $driverId !== '' ? $driverService->getAverageRatings($driverId) : null;
        $rating  = $avg !== null
            ? '<img src="' . ASSETS_PATH . '/icons/EtoileJaune.png" class="img-width-20" alt="Icône étoile"> ' . number_format((float)$avg, 1, ',', '')
            : '<span class="italic">0 avis</span>';

        $seatsAvailable = $carpoolService->seatsAvailable($carId, $id);
        $seatsLabel     = $seatsAvailable <= 1 ? "$seatsAvailable place" : "$seatsAvailable places";

        // --- actions (seulement en détail) ---
        $participateBtn = null;
        $cancelBtn      = null;
        if ($withActions) {
            $isGuest     = $currentUserId === null;
            $isPassenger = !$isGuest && in_array($userRepo->getRole($currentUserId), [1, 3]);
            $canParticipate = ($isGuest || $isPassenger) && !$isOwner && $status === 'not started' && $seatsAvailable > 0;
            $participateBtn = $canParticipate
                ? '<button id="participate" class="btn action-btn" style="padding: 8px;" data-id="'
                . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '">'
                . '<img src="' . ASSETS_PATH . '/icons/Calendrier.png" class="img-pointer" alt="booking calendar icon">'
                . '<span>Participer au covoiturage</span>'
                . '</button>'
                : '<div class="btn btn-desactivated" style="padding: 8px;"><span>Participer au covoiturage</span></div>';

            $canCancel = $isOwner && $status === 'not started';
            $cancelBtn = $canCancel
                ? '<a class="btn action-btn" style="padding: 8px;" href="'
                . BASE_URL . '/back/user/user_space.php?action=cancel_carpool&id='
                . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '">Annuler le covoiturage</a>'
                : null;
        }

        return [
            'id'             => $id,
            'reservationId'  => $reservationId,
            'driver_photo'   => OtherFormatter::displayPhoto($driverPhoto),
            'driver_rating'  => $rating,
            'driver_pseudo' => $driverpseudo,
            'price_label'    => OtherFormatter::formatCredits($price),
            'departure_city' => $depCity,
            'arrival_city'   => $arrCity,
            'departure_time' => DateFormatter::time($depTimeRaw),
            'arrival_time'   => DateFormatter::time($arrTimeRaw),
            'date'           => DateFormatter::short($dateRaw),
            'duration'       => self::carpoolDuration($depTimeRaw, $arrTimeRaw),
            'status'         => $status,

            'eco_label'      => OtherFormatter::formatEcoLabel($carElectric),

            'is_owner'       => (bool)$isOwner,
            'completed'      => $seatsAvailable === 0,
            'seats_label'    => $seatsLabel,

            'detail_url'     => $router->generatePath('/covoiturages/details', ['id' => $id]),
            'card_style'     => $isOwner ? "border:2px solid var(--col-green);cursor:pointer;" : "cursor:pointer;",

            // actions (null en liste)
            'participate_btn' => $participateBtn,
            'cancel_btn'     => $cancelBtn,
        ];
    }

    /** @return array[] */
    public static function many(
        array $rows,                       // rows SQL (pas d'entités)
        ?string $currentUserId,
        DriverService $driverService,
        CarpoolService $carpoolService,
        UserRepository $userRepo,
        Router $router
    ): array {
        // on réutilise ONE, sans actions
        return array_map(
            fn(array $r) => self::one($r, $currentUserId, $driverService, $carpoolService, $userRepo, $router, false),
            $rows
        );
    }

    /**
     * Calculate the difference between the departure time and the arrival time of the carpool
     * @param string $carpoolDepartureTime
     * @param string $carpoolArrivalTime
     * @return string // return a duration (ex. 2h40)
     */
    private static function carpoolDuration(string $carpoolDepartureTime, string $carpoolArrivalTime): string
    {
        // Convert string to DateTime Objects 
        $departure = new DateTime($carpoolDepartureTime);
        $arrival = new DateTime($carpoolArrivalTime);

        // difference calcul
        $interval = $departure->diff($arrival);

        return $interval->format('%hh%I');
    }
}
