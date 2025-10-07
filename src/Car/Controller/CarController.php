<?php

namespace App\Car\Controller;

use App\Controller\BaseController;
use App\Routing\Router;
use PDOException;
use Exception;

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

    public function new()
    {
        $userId  = $_SESSION['user_id'] ?? null;

        $licencePlate = $_POST['licence_plate'];
        $firstRegistrationDate = $_POST['first_registration_date'];
        $brand = $_POST['brand'];
        $model = $_POST['model'];
        $electricValue = $_POST['electric'];
        if ($electricValue === "yes") {
            $electric = 1;
        } elseif ($electricValue === "no") {
            $electric = 0;
        }
        $color = $_POST['color'];
        $seatOffered = $_POST['nb_passengers'];

        try {
            $this->repo->newCar($userId, $brand, $model, $licencePlate, $firstRegistrationDate, $seatOffered, $electric, $color);
        } catch (PDOException $e) {
            error_log("Database error in newCar(): " . $e->getMessage());
            throw new Exception("Une erreur est survenue");
        }
        echo json_encode([
            "success" => true,
            "brand" => $brand,
            "model" => $model,
            "licence_plate" => $licencePlate
        ]);
    }

    public function list()
    {
        $userId = $_SESSION['user_id'] ?? null;

        $carRepo = new CarRepository();
        $cars = $carRepo->findAllCars($userId);

        return $this->renderPartial('components/car/list.php', [
            'cars' => $cars
        ]);
    }
}
