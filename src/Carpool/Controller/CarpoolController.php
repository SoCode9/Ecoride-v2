<?php

namespace App\Carpool\Controller;

use App\Database\DbConnection;
use PDO;
use App\Routing\Router;
use App\Controller\BaseController;
use App\Carpool\Repository\CarpoolRepository;
use App\Driver\Repository\DriverRepository;
use App\Driver\Service\DriverService;
use App\Carpool\Service\CarpoolService;
use App\Utils\Formatting\DateFormatter;
use App\Utils\Formatting\OtherFormatter;

class CarpoolController extends BaseController
{

    public function __construct(Router $router)
    {
        parent::__construct($router);
        // ici, tes initialisations éventuelles
        // $this->service = new CarpoolService(new CarpoolRepository());
    }

    public function list()
    {
        // État courant en session (ou valeurs par défaut)
        $state = $_SESSION['carpools.search'] ?? [
            'date'         => null, // stockée en d.m.Y pour l'affichage humain
            'departure'    => null,
            'arrival'      => null,
            'eco'          => null, // 1 ou null
            'maxPrice'     => null,
            'maxDuration'  => null,
            'driverRating' => null,
        ];

        // ----- POST : on fusionne l'état puis PRG -----
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? null;

            if ($action === 'reset_filters') {
                // On ne touche pas à la recherche principale
                $state['eco']          = null;
                $state['maxPrice']     = null;
                $state['maxDuration']  = null;
                $state['driverRating'] = null;
            } elseif ($action === 'search') {
                // Formulaire principal : départ, arrivée, date (Y-m-d depuis <input type="date">)
                $state['departure'] = ($v = trim($_POST['departure'] ?? '')) !== '' ? $v : null;
                $state['arrival']   = ($v = trim($_POST['arrival']   ?? '')) !== '' ? $v : null;

                $datePost = trim($_POST['date'] ?? ''); // Y-m-d
                $state['date'] = DateFormatter::toDb($datePost); // on stocke en d.m.Y pour l’UI

            } elseif ($action === 'filters') {
                // Formulaire filtres
                $state['eco'] = isset($_POST['eco']) ? 1 : null;

                $state['maxPrice'] = (isset($_POST['maxPrice']) && $_POST['maxPrice'] !== '')
                    ? max(1, (int)$_POST['maxPrice']) : null;

                $state['maxDuration'] = (isset($_POST['maxDuration']) && $_POST['maxDuration'] !== '')
                    ? max(1, (int)$_POST['maxDuration']) : null;

                $driverRating = $_POST['driverRating'] ?? '';
                $state['driverRating'] = ($driverRating === '' || $driverRating === 'none')
                    ? null
                    : (float)$driverRating;
            }

            // Sauvegarde en session puis PRG (évite le repost et garde une URL propre)
            $_SESSION['carpools.search'] = $state;
            header('Location: ' . $this->router->generatePath('/covoiturages'));
            exit;
        }

        // ----- GET : on lit l'état et on cherche -----
        $filters = $state; // alias lisible

        $repo    = new CarpoolRepository($this->router);
        $service = new CarpoolService($repo);
        $rawCarpools = $repo->search(
            $filters['date'] ?? null,
            $filters['departure'] ?? null,
            $filters['arrival'] ?? null,
            $filters['eco'] ?? null,
            $filters['maxPrice'] ?? null,
            $filters['maxDuration'] ?? null,
            $filters['driverRating'] ?? null,
        );
        $nextCarpool = null;

        if (empty($rawCarpools)) {
            $nextTravelDate = $repo->searchnextTravelDate(
                $filters['date'] ?? null,
                $filters['departure'] ?? null,
                $filters['arrival'] ?? null,
                $filters['eco'] ?? null,
                $filters['maxPrice'] ?? null,
                $filters['maxDuration'] ?? null,
                $filters['driverRating'] ?? null,
            );
            if ($nextTravelDate && !empty($nextTravelDate['date'])) {
                $ymd = DateFormatter::toDb($nextTravelDate['date']); // au cas où
                $nextCarpool = [
                    'date_ui' => DateFormatter::short($ymd) ?? DateFormatter::toUi($ymd),
                    'date_db' => $ymd, // pour remettre dans <input type="date"> ou en POST
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

        // Post-traitement d'affichage (mapping "prêt à afficher")
        $userId = $_SESSION['user_id'] ?? null;
        $driverRepo    = new DriverRepository($this->router);
        $driverService = new DriverService($driverRepo);
        $carpools = array_map(function (array $c) use ($userId, $driverService) {
            $isOwner = $userId && isset($c['driver_id']) && (string)$c['driver_id'] === (string)$userId;
            $avg = $driverService->getAverageRatings($c['driver_id']);
            return [
                'id'             => htmlspecialchars($c['id'] ?? ''),
                'driver_pseudo'  => htmlspecialchars($c['driver_pseudo'] ?? ''),
                'driver_photo'   => OtherFormatter::displayPhoto($c['driver_photo']) ?? null,
                'driver_rating'  => $driverService->getAverageRatings($c['driver_id'])
                    ? '<img src="' . ASSETS_PATH . '/icons/EtoileJaune.png" class="img-width-20" alt="Icône étoile"> ' . number_format((float)$avg, 1, ',', '')
                    : '<span class="italic">0 avis</span>',

                'price_label'    => OtherFormatter::formatCredits((int)($c['price'] ?? 0)),
                'departure_time' => !empty($c['departure_time']) ? DateFormatter::time($c['departure_time']) : '',
                'arrival_time'   => !empty($c['arrival_time'])   ? DateFormatter::time($c['arrival_time'])   : '',
                'eco_label'      => OtherFormatter::formatEcoLabel((bool)($c['electric'] ?? 0)),

                'is_owner'       => $isOwner,
                'detail_url'     => $this->router->generatePath('/covoiturages/details', ['id' => $c['id']]),

                'card_style'     => $isOwner
                    ? "border:2px solid var(--col-green);cursor:pointer;"
                    : "cursor:pointer;",
                'completed'      => isset($c['seats_available']) && (int)$c['seats_available'] === 0,
                'seats_label'    => isset($c['seats_available'])
                    ? ($c['seats_available'] <= 1
                        ? $c['seats_available'] . " place"
                        : $c['seats_available'] . " places")
                    : ''
            ];
        }, $rawCarpools);

        $hasCriteria = array_filter([
            $filters['date']         ?? null,
            $filters['departure']    ?? null,
            $filters['arrival']      ?? null,
            $filters['eco']          ?? null,
            $filters['maxPrice']     ?? null,
            $filters['maxDuration']  ?? null,
            $filters['driverRating'] ?? null,
        ], fn($v) => $v !== null && $v !== '') !== [];

        // Afficher le message "aucun résultat" seulement si recherche faite + zéro résultat
        $showNoResults = $hasCriteria && empty($carpools);

        // Prépare la value de l'<input type="date"> : l'input veut Y-m-d
        $dateInput = DateFormatter::toDb($filters['date']);

        // Texte "Départ le ..."
        $ymd = DateFormatter::toDb($filters['date']); // convertit d.m.Y → Y-m-d (ou null)
        $dateLong = $ymd
            ? 'Départ le ' . DateFormatter::long($ymd)  // long() attend Y-m-d
            : 'Aucune date sélectionnée';

        // Render
        return $this->render('pages/carpools/list.php', 'Covoiturages', [
            'carpools'  => $carpools,
            'filters'   => $filters,
            'dateLong'  => $dateLong,
            'dateInput' => $dateInput,
            'showNoResults' => $showNoResults,
            'nextCarpool' => $nextCarpool
        ]);
    }

    public function details()
    {
        return $this->render('pages/carpools/details.php', 'Détail', []);
    }
}
