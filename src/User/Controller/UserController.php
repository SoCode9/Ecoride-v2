<?php

namespace App\User\Controller;

use App\Controller\BaseController;

class UserController extends BaseController
{
    public function profil()
    {
        return $this->render('pages/user_space/profile.php', 'Mon espace', []);
    }
}
