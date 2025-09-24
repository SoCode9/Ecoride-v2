<?php

namespace App\Carpool\Service;

use App\Car\Repository\CarRepository;
use App\Carpool\Repository\CarpoolRepository;
use App\Driver\Service\DriverService;
use App\Reservation\Repository\ReservationRepository;
use App\Routing\Router;
use App\Utils\Formatting\DateFormatter;
use App\Utils\Formatting\OtherFormatter;

final class CarpoolService
{
    public function __construct(private CarpoolRepository $repo) {}
    /**
     * Run the main search with the given filters and, if no results are found,
     * compute the next available carpool date that matches the same criteria.
     * Expected filter keys:
     *  - 'date' (Y-m-d or null), 'departure', 'arrival',
     *  - 'eco' (1|null), 'maxPrice' (int|null), 'maxDuration' (int|null), 'driverRating' (float|null)
     * 
     * @param array $filters $filters Normalized search filters (ideally date in Y-m-d).
     * @return array<array|null> {0: array<int, array<string,mixed>>, 1: array<string,mixed>|null}
     *         Tuple: [ $rows, $nextCarpool ]
     *         - $rows: raw DB rows from repository
     *         - $nextCarpool: null or ['date_ui'=>string,'date_db'=>string,'filters'=>array]
     */
    public function findWithSuggestion(array $filters): array
    {
        // On suppose que $filters['date'] est déjà en Y-m-d (tu fais toDb au POST)
        $rows = $this->repo->search(
            $filters['date'] ?? null,
            $filters['departure'] ?? null,
            $filters['arrival'] ?? null,
            $filters['eco'] ?? null,
            $filters['maxPrice'] ?? null,
            $filters['maxDuration'] ?? null,
            $filters['driverRating'] ?? null,
        );

        $nextCarpool = null;
        if (empty($rows)) {
            $row = $this->repo->searchNextTravelDate(
                $filters['date'] ?? null,
                $filters['departure'] ?? null,
                $filters['arrival'] ?? null,
                $filters['eco'] ?? null,
                $filters['maxPrice'] ?? null,
                $filters['maxDuration'] ?? null,
                $filters['driverRating'] ?? null,
            );
            if ($row && !empty($row['date'])) {
                $ymd = DateFormatter::toDb($row['date']); // sécurité
                $nextCarpool = [
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
            }
        }

        return [$rows, $nextCarpool];
    }

    /** 
     *Transform raw DB rows into "cards" ready for the view (no business logic here).
     * Keeps your existing rendering conventions: preformatted labels, times, and URLs. * Summary of mapForList
     * 
     * @param array $rows Raw rows returned by the repository.
     * @param mixed $userId Current logged-in user id (used to mark the owner's cards).
     * @param \App\Driver\Service\DriverService $driverService Service used to retrieve driver ratings.
     * @param \App\Routing\Router $router Router used to build detail URLs.
     * @return array Cards prepared for the template (safe/escaped where needed). 
     */
    public function mapForList(array $rows, ?string $userId, DriverService $driverService, Router $router): array
    {
        return array_map(function (array $c) use ($userId, $driverService, $router) {

            $isOwner = $userId && isset($c['driver_id']) && (string)$c['driver_id'] === (string)$userId;
            $carRepo = new CarRepository($router);
            $resRepo = new ReservationRepository($router);

            // moyenne (simple — si tu veux éviter N+1, on fera un batch plus tard)
            $avg = $driverService->getAverageRatings((string)$c['driver_id']);
            $ratingHtml = $avg !== null
                ? '<img src="' . ASSETS_PATH . '/icons/EtoileJaune.png" class="img-width-20" alt="Icône étoile"> ' . number_format((float)$avg, 1, ',', '')
                : '<span class="italic">0 avis</span>';
            $seatsAvailable = OtherFormatter::seatsAvailable($carRepo->getSeatsOfferedByCar($c['car_id']), $resRepo->countPassengers($c['id']));
            return [
                'id'             => htmlspecialchars($c['id'] ?? ''),
                'driver_pseudo'  => htmlspecialchars($c['driver_pseudo'] ?? ''),
                'driver_photo'   => OtherFormatter::displayPhoto($c['driver_photo'] ?? null),
                'driver_rating'  => $ratingHtml,

                'price_label'    => OtherFormatter::formatCredits((int)($c['price'] ?? 0)),
                'departure_time' => !empty($c['departure_time']) ? DateFormatter::time($c['departure_time']) : '',
                'arrival_time'   => !empty($c['arrival_time'])   ? DateFormatter::time($c['arrival_time'])   : '',
                'eco_label'      => OtherFormatter::formatEcoLabel((bool)($c['car_electric'] ?? 0)),

                'is_owner'   => $isOwner,
                'detail_url' => $router->generatePath('/covoiturages/details', ['id' => $c['id']]),

                'card_style' => $isOwner
                    ? "border:2px solid var(--col-green);cursor:pointer;"
                    : "cursor:pointer;",

                'completed'   => (int)$seatsAvailable === 0,
                'seats_label' => $seatsAvailable <= 1
                    ? $seatsAvailable . " place"
                    : $seatsAvailable . " places"
            ];
        }, $rows);
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
