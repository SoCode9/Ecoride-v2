<?php

namespace App\Dashboard;

use App\Controller\BaseController;

class DashboardController extends BaseController
{
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
}
