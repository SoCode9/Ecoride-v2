<?php

namespace App\Carpool\Controller;

use App\Controller\BaseController;
use App\Routing\Router;

use App\Carpool\Repository\CarpoolRepository;
use App\Driver\Repository\DriverRepository;
use App\Reservation\Repository\ReservationRepository;
use App\Car\Repository\CarRepository;
use App\User\Repository\UserRepository;
use App\Rating\Repository\RatingRepository;

use App\Driver\Service\DriverService;
use App\Carpool\Service\CarpoolService;
use App\Utils\Formatting\DateFormatter;
use App\Utils\Formatting\OtherFormatter;
use App\Rating\Service\RatingService;


class CarpoolController extends BaseController
{
    private const SEARCH_KEY = 'carpools.search';
    private CarpoolService $service;
    private CarpoolRepository $repo;

    public function __construct(Router $router)
    {
        parent::__construct($router);
        $userRepo = new UserRepository();
        $driverService = new DriverService(new DriverRepository($userRepo));
        $this->repo = new CarpoolRepository();
        $this->service = new CarpoolService(
            $this->repo,
            $driverService,
            new ReservationRepository(),
            new CarRepository(),
            new UserRepository(),
            $router
        );
    }

    public function list()
    {
        $state = $_SESSION[self::SEARCH_KEY] ?? [
            'date'         => null,
            'departure'    => null,
            'arrival'      => null,
            'eco'          => null,
            'maxPrice'     => null,
            'maxDuration'  => null,
            'driverRating' => null,
        ];

        // POST -> merge + PRG
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $state = $this->mergePostIntoState($state);
            $_SESSION[self::SEARCH_KEY] = $state;
            $this->redirectToList();
        }

        $filters = $state;
        $userId  = $_SESSION['user_id'] ?? null;

        $carpools = $this->service->listView($filters, $userId);
        $nextCarpool = empty($carpools) ? $this->service->findNextCarpool($filters) : null;

        $ui = $this->service->buildUiMeta($filters, $carpools);

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
        $id = (string)($_GET['id'] ?? '');
        if ($id === '') {
            throw new \InvalidArgumentException('ID manquant');
        }

        $userId  = $_SESSION['user_id'] ?? null;
        $carpoolFormatted = $this->service->detailView($id, $userId);

        $carpool = $this->repo->findById($id);
        $userRepo = new UserRepository();
        $driRepo = new DriverRepository($userRepo);
        $driSer = new DriverService($driRepo);
        $carRepo = new CarRepository();
        $ratingRepo = new RatingRepository();
        $ratingSer = new RatingService($ratingRepo, $userRepo);
        $driver = $driRepo->makeFromUserId($carpool->getIdDriver());
        $car = $carRepo->findById($carpool->getCarId());
        $electric = $car->isElectric() ? ' - Electrique' : '';
        $getterByPref = [
            'food'    => 'getFood',
            'music'   => 'getMusic',
            'pets'    => 'getPets',
            'smoker'  => 'getSmoker',
            'speaker' => 'getSpeaker',
        ];
        $preferencesData = [];

        foreach ($getterByPref as $preference => $getter) {
            $result = $driver->$getter(); // ?bool (true/false/null)
            $item = $driSer->formatPreference($preference, $result);
            if ($item !== null) {
                $preferencesData[] = $item;
            }
        }

        $dateLong  = $carpool->getDate() ? ('Départ le ' . DateFormatter::long($carpool->getDate())) : 'Aucune date sélectionnée';

        $ratings = $ratingRepo->findAllByDriver($carpool->getIdDriver(), true);
        $ratings = $ratings ?? [];
        $ratingsFormatted = $ratingSer->formatRatings($ratings);
       
        return $this->render('pages/carpools/details.php', 'Détail', [
            'carpool' => $carpool,
            'carpoolFormatted' => $carpoolFormatted,
            'driver' => $driver,
            'dateLong' => $dateLong,
            'car' => $car,
            'isElectric' => $electric,
            'preferencesData' => $preferencesData,
            'ratings' => $ratings,
            'ratingsFormatted' => $ratingsFormatted
        ]);
    }


    private function mergePostIntoState(array $state): array
    {
        $action = $_POST['action'] ?? null;

        if ($action === 'reset_filters') {
            $state['eco']          = null;
            $state['maxPrice']     = null;
            $state['maxDuration']  = null;
            $state['driverRating'] = null;
            return $state;
        }

        if ($action === 'search') {
            $dep = trim($_POST['departure'] ?? '');
            $arr = trim($_POST['arrival'] ?? '');
            $datePost = trim($_POST['date'] ?? ''); // Y-m-d depuis <input type="date">

            $state['departure'] = ($dep !== '') ? $dep : null;
            $state['arrival']   = ($arr !== '') ? $arr : null;
            // toDb doit être idempotent si c'est déjà du Y-m-d
            $state['date'] = $datePost !== '' ? DateFormatter::toDb($datePost) : null;
            return $state;
        }

        if ($action === 'filters') {
            $state['eco'] = isset($_POST['eco']) ? 1 : null;

            $state['maxPrice'] = ($_POST['maxPrice'] ?? '') !== ''
                ? max(1, (int)$_POST['maxPrice']) : null;

            $state['maxDuration'] = ($_POST['maxDuration'] ?? '') !== ''
                ? max(1, (int)$_POST['maxDuration']) : null;

            $driverRating = $_POST['driverRating'] ?? '';
            $state['driverRating'] = ($driverRating === '' || $driverRating === 'none')
                ? null
                : (float)$driverRating;

            return $state;
        }

        return $state;
    }

    private function redirectToList(): void
    {
        header('Location: ' . $this->router->generatePath('/covoiturages'));
        exit;
    }
}
