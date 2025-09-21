<?php

namespace App\Dashboard;

use App\Controller\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        return $this->render('pages/index.php', []);
    }

    /**
     * Function to manage the responsive menu display
     * @param mixed $asListItem true if small screen
     * @return void
     */
    function renderNavigationLinks($asListItem = false)
    {
        //$base = BASE_URL;
        $tagOpen = $asListItem ? '<li>' : '';
        $tagClose = $asListItem ? '</li>' : '';

        echo $tagOpen . "<a id='home-page' href='#'>Accueil</a>" . $tagClose;
        echo $tagOpen . "<a id='carpool-button' href='#'>Covoiturages</a>" . $tagClose;
        echo $tagOpen . "<a id='contact-button' href='#'>Contact</a>" . $tagClose;

       /*  if (isset($_SESSION['user_id'])) {
            switch ($_SESSION['role_user']) {
                case 1:
                case 2:
                case 3:
                    echo $tagOpen . "<a class='btn border-white' id='user-space' href='{$base}/controllers/user_space.php'>Espace Utilisateur</a>" . $tagClose;
                    break;
                case 4:
                    echo $tagOpen . "<a class='btn border-white' id='employee-space' href='{$base}/controllers/employee_space.php'>Espace Employé</a>" . $tagClose;
                    break;
                case 5:
                    echo $tagOpen . "<a class='btn border-white' id='admin-space' href='{$base}/controllers/admin_space.php'>Espace Administrateur</a>" . $tagClose;
                    break;
            }
            echo $tagOpen . "<a id='logout-button' href='{$base}/controllers/login.php'> 
            <img src='{$base}/icons/Deconnexion.png' alt='logout' class='logout-btn'> 
        </a>" . $tagClose;
        } else {
            echo $tagOpen . "<a class='btn border-white' id='login-button' href='{$base}/controllers/login.php'>Connexion</a>" . $tagClose;
        } */
    }
}
