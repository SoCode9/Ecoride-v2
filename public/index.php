<?php


if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';

use App\Carpool\Controller\CarpoolController;
use App\Reservation\Controller\ReservationController;
use App\Dashboard\DashboardController;
use App\User\Controller\UserController;
use App\Car\Controller\CarController;
use App\Driver\Controller\DriverController;
use Symfony\Component\Dotenv\Dotenv;
use App\Routing\Router;
use App\User\Entity\User;


//données de test de session
$connectedId = $_SESSION['user_id'] = '947b27ba-8a5d-11f0-be17-50ebf69c727b';
/* $_SESSION = [];           // vide le tableau
session_destroy();        // supprime le fichier de session
setcookie('PHPSESSID', '', time() - 3600, '/'); // (optionnel) supprime le cookie */

//charge configuration
$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

// variable vers dossier assets dans public
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'); //enlève la dernière partie (index.php) pour ne garder que le dossier "/Ecoride-v2/public" 
define('BASE_URL', $baseUrl); // stocke /2coursPhp/public
define('ASSETS_PATH', BASE_URL . '/assets/');              // -> /2coursPhp/public/assets/

define('MAIN_TEMPLATE_PATH', __DIR__ . '/../templates/main.php');
define('TEMPLATE_PATH', __DIR__ . '/../templates');

define('PHOTOS_URL', BASE_URL . '/assets/photos'); // URL publique
define('PHOTOS_DIR', __DIR__   . '/assets/photos'); // chemin disque (public/assets/photos)

$router = new Router($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']); //URI : tout ce qui est derrière le nom de domaine

$router->register(['GET'], '/', DashboardController::class, 'index');
$router->register(['GET'], '/mon-profil', UserController::class, 'profile');
$router->register(['POST'], '/mon-profil/update', UserController::class, 'editProfile');
$router->register(['POST'], '/mon-profil/photo', UserController::class, 'editPhoto');
$router->register(['GET'], '/mes-covoiturages', UserController::class, 'listCarpools');
$router->register(['GET', 'POST'], '/covoiturages', CarpoolController::class, 'list');
$router->register(['GET'], '/covoiturages/details', CarpoolController::class, 'details');
$router->register(['GET'], '/mentions-legales', DashboardController::class, 'legalInformations');
$router->register(['POST'], '/reservation/check', ReservationController::class, 'checkParticipation');
$router->register(['POST'], '/reservation/update', ReservationController::class, 'updateParticipation');
$router->register(['POST'], '/car/add', CarController::class, 'new');
$router->register(['GET'], '/car/list', CarController::class, 'list');
$router->register(['POST'], '/car/delete', CarController::class, 'delete');
$router->register(['POST'], '/preference/add', DriverController::class, 'newOtherPreference');
$router->register(['GET'], '/preference/list', DriverController::class, 'listOtherPreferences');
$router->register(['POST'], '/preference/delete', DriverController::class, 'deleteOtherPreference');

// exemple d'une route pour créer un user
// $router->register(['GET', 'POST'], '/user/create', UserController::class, 'create');

/* echo '<pre>';
print_r($router->getRoutes());
echo '</pre>'; */

echo $router->run(); //va gérer l'affichage derrière
