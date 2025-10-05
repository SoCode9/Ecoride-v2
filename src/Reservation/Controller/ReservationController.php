<?php

namespace App\Reservation\Controller;

use App\Controller\BaseController;
use App\Routing\Router;

use App\Reservation\Repository\ReservationRepository;
use App\User\Repository\UserRepository;
use App\Carpool\Repository\CarpoolRepository;
use App\Car\Repository\CarRepository;

use App\Reservation\Service\ReservationService;


class ReservationController extends BaseController
{
    private ReservationService $service;
    private ReservationRepository $repo;

    public function __construct(Router $router)
    {
        $userRepo = new UserRepository();
        $carpoolRepo = new CarpoolRepository();
        $carRepo = new CarRepository();
        $this->repo = new ReservationRepository();

        $this->service = new ReservationService($this->repo, $userRepo, $carpoolRepo, $carRepo);
    }

    public function checkParticipation()
    {

        $this->service->checkParticipation();
    }
}
