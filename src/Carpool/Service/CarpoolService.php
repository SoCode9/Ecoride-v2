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
        if (!$c) throw new \Exception('Covoiturage introuvable');
        return CarpoolDisplay::one($c, $userId, $this->driverService, $this->resRepo, $this->carRepo, $this->userRepo, $this->router, true);
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
            $this->resRepo,
            $this->carRepo,
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
}
