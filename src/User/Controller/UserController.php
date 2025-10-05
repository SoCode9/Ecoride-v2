<?php

namespace App\User\Controller;

use App\Controller\BaseController;
use App\Routing\Router;

use App\User\Repository\UserRepository;
use App\Car\Repository\CarRepository;
use App\Driver\Repository\DriverRepository;
use App\User\Service\UserService;

class UserController extends BaseController
{

    private UserService $service;
    private UserRepository $repo;

    public function __construct(Router $router)
    {
        parent::__construct($router);
        $this->repo = new UserRepository();
        $this->service = new UserService($this->repo);
    }

    public function profil()
    {

        $userId  = $_SESSION['user_id'] ?? null;
        $user = $this->repo->findById($userId);

        $driRepo = new DriverRepository($this->repo);
        $driver =  $driRepo->makeFromUserId($userId);

        $formattedUser = $this->service->displayProfil($user);

        $car = new CarRepository();
        $cars = $car->findAllCars($userId);

        return $this->render('pages/user_space/profile.php', 'Mon espace', [
            'user' => $user,
            'formattedUser' => $formattedUser,
            'driver' => $driver,
            'cars' => $cars
        ]);
    }
}
