<?php

namespace App\Dashboard;

use App\Controller\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        return $this->render('pages/index.php','EcoRide', []);
    }

    public function legalInformations()
    {
        return $this->render('pages/legal_informations/legal_informations.php', 'Mentions légales',[]);
    }
}
