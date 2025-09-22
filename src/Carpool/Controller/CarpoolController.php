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

        // 3) Appel service + repo (repo SANS router)
        $repo = new CarpoolRepository($this->router);
        $service = new CarpoolService($repo);
        $carpools = $service->searchWithFormatting($filters);

        // 4) Texte d’en-tête "Départ le ..."
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

   

        // 5) Render
        return $this->render('pages/carpools/list.php', 'Covoiturages', [
            'carpools' => $carpools,
            'filters'  => $filters,
            'dateLong' => $dateLong,
            'dateInput' => $dateInput
        ]);
    }



    /* public function list()
    {



         $_SESSION['departure-date-search'] = '2025-09-22'; //@todo
        $_SESSION['departure-city-search'] = 'Saint-Julien-en-Genevois'; //@todo
        $_SESSION['arrival-city-search'] = 'Lyon'; //@todo

        if (isset($_SESSION['departure-date-search'])) {
            $date = $_SESSION['departure-date-search'];
        } else {
            $date = '';
        }

        if (isset($_SESSION['departure-city-search'])) {
            $departureCity = $_SESSION['departure-city-search'];
        } else {
            $departureCity = '';
        }

        if (isset($_SESSION['arrival-city-search'])) {
            $arrivalCity = $_SESSION['arrival-city-search'];
        } else {
            $arrivalCity = '';
        }

        $filters = [
            'date'         => $_GET['date']         ?? '22.09.2025' /* @TODO remettre null , // dd.mm.yyyy
            'departure'    => $_GET['departure']    ?? 'Saint-Julien-en-Genevois'/* @TODO remettre null ,
            'arrival'      => $_GET['arrival']      ?? 'Lyon'/* @TODO remettre null ,
            'eco'          => isset($_GET['eco']) ? (int)$_GET['eco'] : null,
            'maxPrice'     => isset($_GET['maxPrice']) ? (int)$_GET['maxPrice'] : null,
            'maxDuration'  => isset($_GET['maxDuration']) ? (int)$_GET['maxDuration'] : null,
            'driverRating' => isset($_GET['driverRating']) ? (float)$_GET['driverRating'] : null,
        ];

        // Service (avec formatage pour le front)
        $repo = new CarpoolRepository($this->router);
        $service = new CarpoolService($repo);
        $carpools = $service->searchWithFormatting($filters);

        if (isset($_SESSION['departure-date-search'])) {
            $dateSearchedFormatted = 'Départ le ' . DateFormatter::long($_SESSION['departure-date-search']);
        } else {
            $dateSearchedFormatted = 'Aucune date sélectionnée';
        }


        return $this->render('pages/carpools/list.php', 'Covoiturages', [
            'carpools' => $carpools,
            'date' => $date,
            'departureCity' => $departureCity,
            'arrivalCity' => $arrivalCity,
            'dateLong' => $dateSearchedFormatted
        ]);
    } */
}
