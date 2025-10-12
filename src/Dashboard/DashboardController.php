<?php

namespace App\Dashboard;

use Exception;
use App\Routing\Router;
use App\Utils\MailService;

use App\Controller\BaseController;

use App\Dashboard\DashboardService;

class DashboardController extends BaseController
{

    private DashboardService $service;

    public function __construct(Router $router)
    {
        parent::__construct($router);
        $this->service = new DashboardService(new MailService());
    }

    public function index()
    {
        $state = $_SESSION['carpools.search'] ?? [
            'date' => null,
            'departure' => null,
            'arrival' => null
        ];
        return $this->render('pages/index.php', 'EcoRide', ['state' => $state]);
    }

    public function legalInformations()
    {
        return $this->render('pages/legal_informations/legal_informations.php', 'Mentions légales', []);
    }

    public function contact()
    {
        return $this->render('pages/contact/contact.php', 'Nous contacter', []);
    }

    public function sendContact()
    {
        try {
            $this->service->sendContact();
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location:' . BASE_URL . '/');
            exit;
        }
    }
}
