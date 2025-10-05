<?php

namespace App\Car\Controller;

use App\Controller\BaseController;
use App\Routing\Router;

use App\Utils\Formatting\DateFormatter;

use App\Car\Repository\CarRepository;
use App\Car\Service\CarService;
use App\Driver\Repository\DriverRepository;

class CarController extends BaseController
{

    private CarService $service;
    private CarRepository $repo;

    public function __construct(Router $router)
    {
        parent::__construct($router);
        $this->repo = new CarRepository();
        $this->service = new CarService($this->repo);
    }

   
}
