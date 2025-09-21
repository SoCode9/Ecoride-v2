<?php

namespace App\User\Controller;

use App\Controller\BaseController;

class UserController extends BaseController
{
    public function list()
    {
        return $this->render('pages/users/list.php', 'Utilisateurs', []);
    }
}
