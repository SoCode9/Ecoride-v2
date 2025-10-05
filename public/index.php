<?php

session_start();
//données de test de session
/* $_SESSION['user_id'] = '947b27ba-8a5d-11f0-be17-50ebf69c727b';
$_SESSION = [];           // vide le tableau
session_destroy();        // supprime le fichier de session
setcookie('PHPSESSID', '', time() - 3600, '/'); // (optionnel) supprime le cookie */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Carpool\Controller\CarpoolController;
use App\Reservation\Controller\ReservationController;
use App\Dashboard\DashboardController;
use App\User\Controller\UserController;
use Symfony\Component\Dotenv\Dotenv;
use App\Routing\Router;

//charge configuration
$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

// variable vers dossier assets dans public
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'); //enlève la dernière partie (index.php) pour ne garder que le dossier "/Ecoride-v2/public" 
define('BASE_URL', $baseUrl); // stocke /2coursPhp/public
define('ASSETS_PATH', BASE_URL . '/assets/');              // -> /2coursPhp/public/assets/

define('TEMPLATE_PATH', __DIR__ . '/../templates/main.php');

define('PHOTOS_URL', BASE_URL . '/assets/photos'); // URL publique
define('PHOTOS_DIR', __DIR__   . '/assets/photos'); // chemin disque (public/assets/photos)

$router = new Router($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']); //URI : tout ce qui est derrière le nom de domaine

// GET = méthode utilisée dans le navigateur
// /user/list = l'url dans le navigateur après l'host (on parle d'URI)
// MaClass = la classe à charger pour appeler le dernier paramètre
// listUser = la fonction (méthode) à appeler dans la classe MaClass
$router->register(['GET'], '/', DashboardController::class, 'index');
$router->register(['GET'], '/user/list', UserController::class, 'list');
$router->register(['GET', 'POST'], '/covoiturages', CarpoolController::class, 'list');
$router->register(['GET'], '/covoiturages/details', CarpoolController::class, 'details');
$router->register(['GET'], '/mentions-legales', DashboardController::class, 'legalInformations');
$router->register(['POST'], '/reservation/check', ReservationController::class, 'checkParticipation');
// exemple d'une route pour créer un user
// $router->register(['GET', 'POST'], '/user/create', UserController::class, 'create');

/* echo '<pre>';
print_r($router->getRoutes());
echo '</pre>'; */

echo $router->run(); //va gérer l'affichage derrière
