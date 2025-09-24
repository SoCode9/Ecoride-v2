<?php

namespace App\Car\Service;

use App\Car\Repository\CarRepository;

use App\Routing\Router;
use App\Utils\Formatting\DateFormatter;
use App\Utils\Formatting\OtherFormatter;

final class CarService
{
    public function __construct(private CarRepository $repo) {}


}