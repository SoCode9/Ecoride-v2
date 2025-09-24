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
        $filters = $state;

        $repo          = new CarpoolRepository($this->router);
        $service       = new CarpoolService($repo);
        $driverService = new DriverService(new DriverRepository($this->router));

        // 1) recherche + suggestion
        [$rows, $nextCarpool] = $service->findWithSuggestion($filters);

        // 2) mapping “cartes”
        $carpools = $service->mapForList($rows, $_SESSION['user_id'] ?? null, $driverService, $this->router);

        // 3) méta UI
        $ui = $service->buildUiMeta($filters, $carpools);

        // 4) render
        return $this->render('pages/carpools/list.php', 'Covoiturages', [
            'carpools'      => $carpools,
            'filters'       => $filters,
            'dateLong'      => $ui['dateLong'],
            'dateInput'     => $ui['dateInput'],
            'showNoResults' => $ui['showNoResults'],
            'nextCarpool'   => $nextCarpool,
        ]);
    }

    public function details()
    {
        return $this->render('pages/carpools/details.php', 'Détail', []);
    }
}
