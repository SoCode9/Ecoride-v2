<?php

namespace App\Carpool\Controller;

use App\Database\DbConnection;
use PDO;
use App\Routing\Router;
use App\Controller\BaseController;
use App\Carpool\Repository\CarpoolRepository;
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
        // 1) Lire les filtres depuis GET (nouvelle recherche si au moins un présent)
        $filters = [
            'date'         => isset($_GET['date'])        ? trim($_GET['date'])    : '22.09.2025', /* @TODO remettre null */ // attendu: dd.mm.yyyy
            'departure'    => isset($_GET['departure'])    ? trim($_GET['departure'])    : 'Saint-Julien-en-Genevois', /* @TODO remettre null*/
            'arrival'      => isset($_GET['arrival'])      ? trim($_GET['arrival'])      : 'Lyon',/* @TODO remettre null*/
            'eco'          => isset($_GET['eco'])          ? 1          : null,
            'maxPrice'     => isset($_GET['maxPrice'])     ? (int)$_GET['maxPrice']      : null,
            'maxDuration'  => isset($_GET['maxDuration'])  ? (int)$_GET['maxDuration']   : null,
            'driverRating' => isset($_GET['driverRating']) ? (float)$_GET['driverRating'] : null,
        ];

        $isNewSearch = array_filter($filters, fn($v) => $v !== null) !== [];

        // 2) Session: on sauvegarde si nouvelle recherche, sinon on reprend l’ancienne
        if ($isNewSearch) {
            $_SESSION['carpools.search'] = $filters;
        } else {
            $saved = $_SESSION['carpools.search'] ?? [];
            foreach ($filters as $k => $v) {
                if ($v === null && array_key_exists($k, $saved)) {
                    $filters[$k] = $saved[$k];
                }
            }
        }

        // 3) Récupération des données
        $repo = new CarpoolRepository($this->router);
        $service = new CarpoolService($repo);
        $rawCarpools = $service->searchWithFormatting($filters);

        // 4) Post-traitement d'affichage
        $userId = $_SESSION['user_id'] ?? null;

        $carpools = array_map(function (array $c) use ($userId) {
            $isOwner = $userId && isset($c['driver_id']) && (string)$c['driver_id'] === (string)$userId;

            return [
                'id'             => htmlspecialchars($c['id'] ?? ''),
                'driver_pseudo'  => htmlspecialchars($c['driver_pseudo'] ?? ''),
                'driver_photo'   => $c['driver_photo'] ?? null,

                'price_label'    => OtherFormatter::formatCredits((int)($c['price'] ?? 0)),
                'departure_time' => !empty($c['departure_time']) ? DateFormatter::time($c['departure_time']) : '',
                'arrival_time'   => !empty($c['arrival_time'])   ? DateFormatter::time($c['arrival_time'])   : '',
                'eco_label'      => OtherFormatter::formatEcoLabel((bool)($c['car_electric'] ?? 0)),

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


        // 5) En-tête et input date
        $dateLong = 'Aucune date sélectionnée';
        if (!empty($filters['date'])) {
            $dt = \DateTime::createFromFormat('d.m.Y', $filters['date']);
            if ($dt) {
                // DateFormatter::long attend 'Y-m-d'
                $dateLong = 'Départ le ' . DateFormatter::long($dt->format('Y-m-d'));
            }
        }

        // $filters['date'] peut venir en d.m.Y, d/m/Y ou Y-m-d
        $dateInput = '';
        if (!empty($filters['date'])) {
            foreach (['!d.m.Y', '!d/m/Y', '!Y-m-d'] as $fmt) {
                $dt = \DateTime::createFromFormat($fmt, $filters['date']);
                if ($dt) {
                    $dateInput = $dt->format('Y-m-d');
                    break;
                }
            }
        }



        // 6) Render
        return $this->render('pages/carpools/list.php', 'Covoiturages', [
            'carpools' => $carpools,
            'filters'  => $filters,
            'dateLong' => $dateLong,
            'dateInput' => $dateInput
        ]);
    }

    public function details()
    {
        return $this->render('pages/carpools/details.php', 'Détail', []);
    }
}
