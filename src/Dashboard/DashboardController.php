<?php

namespace App\Dashboard;

use App\Controller\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        return $this->render('pages/index.php', []);
    }
}
