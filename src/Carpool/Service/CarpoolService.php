<?php

namespace App\Carpool\Service;

use App\Car\Repository\CarRepository;
use App\Carpool\Repository\CarpoolRepository;
use App\Driver\Service\DriverService;
use App\Reservation\Repository\ReservationRepository;
use App\User\Repository\UserRepository;
use App\Routing\Router;
use App\Carpool\Service\CarpoolDisplay;
use App\Utils\Formatting\DateFormatter;
use App\Utils\Formatting\OtherFormatter;
use DateTime;

final class CarpoolService
{
    public function __construct(
        private CarpoolRepository $repo,
        private DriverService $driverService,
        private ReservationRepository $resRepo,
        private CarRepository $carRepo,
        private UserRepository $userRepo,
        private Router $router
    ) {}

    /**
     * Finds the next carpool date matching the given filters and returns a small UI model; returns an empty array if none or on error.
     * @param array $filters 
     * @return array{date_db: string|null, date_ui: string|null, filters: array{arrival: mixed, departure: mixed, driverRating: mixed, eco: mixed, maxDuration: mixed, maxPrice: mixed}|null}
     */
    public function findNextCarpool(array $filters): ?array
    {
        try {
            $row = $this->repo->searchNextCarpoolDate(
                $filters['date'] ?? null,
                $filters['departure'] ?? null,
                $filters['arrival'] ?? null,
                $filters['eco'] ?? null,
                $filters['maxPrice'] ?? null,
                $filters['maxDuration'] ?? null,
                $filters['driverRating'] ?? null,
            );

            if (!$row || empty($row['date'])) {
                return null;
            }

            // Normalisation défensive (si 'date' est déjà en Y-m-d, toDb doit la laisser telle quelle)
            $ymd = DateFormatter::toDb((string)$row['date']);

            return [
                'date_ui' => DateFormatter::short($ymd) ?? $ymd,
                'date_db' => $ymd,
                'filters' => [
                    'departure'    => $filters['departure'] ?? null,
                    'arrival'      => $filters['arrival'] ?? null,
                    'eco'          => $filters['eco'] ?? null,
                    'maxPrice'     => $filters['maxPrice'] ?? null,
                    'maxDuration'  => $filters['maxDuration'] ?? null,
                    'driverRating' => $filters['driverRating'] ?? null,
                ],
            ];
        } catch (\Throwable $e) {
            error_log('[CarpoolService::findNextCarpool] ' . $e->getMessage());
            return null;
        }
    }

    public function detailView(string $id, ?string $userId): array
    {
        $c = $this->repo->findById($id);
        if (!$c) throw new \Exception(message: 'Covoiturage introuvable');



        return CarpoolDisplay::one($c, $userId, $this->driverService, $this, $this->userRepo, $this->router, true);
    }

    /** @return array[] */
    public function listView(array $filters, ?string $userId): array
    {
        $rows = $this->repo->search(
            $filters['date'] ?? null,
            $filters['departure'] ?? null,
            $filters['arrival'] ?? null,
            $filters['eco'] ?? null,
            $filters['maxPrice'] ?? null,
            $filters['maxDuration'] ?? null,
            $filters['driverRating'] ?? null,
        );

        return CarpoolDisplay::many(
            $rows,
            $userId,
            $this->driverService,
            $this,
            $this->userRepo,
            $this->router
        );
    }

    /**
     *  Compute small UI meta values for the template:
     *  - showNoResults: true when a search was performed and no cards are returned
     *  - dateInput: date in Y-m-d, ready for <input type="date">
     *  - dateLong: localized human-readable label (e.g., "Départ le ...")

     * @param array $filters $filters Current (normalized) filters used for the search.
     * @param array $cards Cards produced by mapForList().
     * @return array
     */
    public function buildUiMeta(array $filters, array $cards): array
    {
        $hasCriteria = array_filter([
            $filters['date'] ?? null,
            $filters['departure'] ?? null,
            $filters['arrival'] ?? null,
            $filters['eco'] ?? null,
            $filters['maxPrice'] ?? null,
            $filters['maxDuration'] ?? null,
            $filters['driverRating'] ?? null,
        ], fn($v) => $v !== null && $v !== '') !== [];

        $showNoResults = $hasCriteria && empty($cards);
        $dateInput = $filters['date'] ?? null; // Y-m-d pour <input type="date">
        $dateLong  = $dateInput ? ('Départ le ' . DateFormatter::long($dateInput)) : 'Aucune date sélectionnée';

        return compact('showNoResults', 'dateInput', 'dateLong');
    }

    public function seatsAvailable(int $carId, string $carpoolId)
    {
        $seatsOffered =  $this->carRepo->getSeatsOfferedByCar($carId);
        $passengers = $this->resRepo->countPassengers($carpoolId);
        $seatsAvailable = max(0, $seatsOffered - $passengers);

        return $seatsAvailable;
    }


    // User Space - My Carpools

    /**
     * Return carpools to be validated with the correct formatting
     * @param string $userId The UUID user
     * @return array 
     */
    public function listCarpoolToValidate(string $userId): array
    {
        $rows = $this->repo->getCarpoolsToValidate($userId);

        return CarpoolDisplay::many(
            $rows,
            $userId,
            $this->driverService,
            $this,
            $this->userRepo,
            $this->router
        );
    }

    /**
     * Return carpools not started with the correct formatting
     * @param string $userId The UUID user
     * @return array 
     */
    public function listCarpoolNotStarted(string $userId): array
    {
        $rows = $this->repo->getCarpoolsNotStarted($userId);

        return CarpoolDisplay::many(
            $rows,
            $userId,
            $this->driverService,
            $this,
            $this->userRepo,
            $this->router
        );
    }

    /**
     * Return carpools completed with the correct formatting
     * @param string $userId The UUID user
     * @return array 
     */
    public function listCarpoolCompleted(string $userId): array
    {
        $rows = $this->repo->getCarpoolsCompleted($userId);

        return CarpoolDisplay::many(
            $rows,
            $userId,
            $this->driverService,
            $this,
            $this->userRepo,
            $this->router
        );
    }

    public function checkNewCarpool()
    {
        // 1) Required fields
        $required = [
            'travel-date',
            'departure-city-search',
            'arrival-city-search',
            'travel-departure-time',
            'travel-arrival-time',
            'travel-price',
            'carSelected',
        ];

        $errors = [];
        $old = [];

        foreach ($required as $name) {
            $value = $_POST[$name] ?? '';
            $old[$name] = $value;
            if (trim((string)$value) === '') {
                $errors[$name] = "Ce champ est obligatoire.";
            }
        }

        // 2) Other validations
        if (!isset($errors['travel-date'])) {
            $today = date('Y-m-d');
            if (trim($_POST['travel-date']) < $today) {
                $errors['travel-date'] = "La date ne peut pas être dans le passé.";
            }
        }

        if (!isset($errors['travel-price'])) {
            // entier ≥ 2
            if (!ctype_digit($_POST['travel-price']) || $_POST['travel-price'] < 2) {
                $errors['travel-price'] = "Le prix doit être un entier positif supérieur ou égal à 2.";
            }
        }

        if (
            !isset($errors['departure-city-search']) &&
            !isset($errors['arrival-city-search']) &&
            mb_strtolower(trim($_POST['departure-city-search'])) === mb_strtolower(trim($_POST['arrival-city-search']))
        ) {
            $errors['arrival-city-search'] = "La ville d'arrivée doit être différente de la ville de départ.";
        }

        if (
            !isset($errors['travel-departure-time']) &&
            !isset($errors['travel-arrival-time']) &&
            (trim($_POST['travel-departure-time'])) >= mb_strtolower(trim($_POST['travel-arrival-time']))
        ) {
            $errors['travel-arrival-time'] = "L'heure d'arrivée doit être supérieure à l'heure de départ.";
        }

        // 3) If error → feedback + retour
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old'] = $old;
            $_SESSION['error_message'] = "Veuillez corriger les erreurs de complétion du formulaire.";
            header('Location: ' . BASE_URL . '/mes-covoiturages/nouveau');
            exit;
        }

        // 4) Check CSRF
        if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'])) {
            $_SESSION['form_old'] = $old;
            $_SESSION['error_message'] = "Une erreur est survenue";
            error_log("CSRF check failed in new carpool form (user ID: " . ($_SESSION['user_id'] ?? 'inconnu') . ")");
            header('Location: ' . BASE_URL . '/mes-covoiturages/nouveau');
            exit;
        }
    }
}
