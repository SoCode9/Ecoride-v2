<?php


if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';

use App\Carpool\Controller\CarpoolController;
use App\Login\Controller\LoginController;
use App\Reservation\Controller\ReservationController;
use App\Dashboard\DashboardController;
use App\User\Controller\UserController;
use App\Car\Controller\CarController;
use App\Driver\Controller\DriverController;

use Symfony\Component\Dotenv\Dotenv;
use App\Routing\Router;

//charge configuration
$envPath = dirname(__DIR__) . '/.env';

// Only load .env if it is present locally.
if (is_readable($envPath)) {
    (new Dotenv())->usePutenv()->load($envPath);
}

// variable vers dossier assets dans public
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'); //enlève la dernière partie (index.php) pour ne garder que le dossier "/Ecoride-v2/public" 
define('BASE_URL', $baseUrl);
define('ASSETS_PATH', BASE_URL . '/assets/');

define('MAIN_TEMPLATE_PATH', __DIR__ . '/../templates/main.php');
define('TEMPLATE_PATH', __DIR__ . '/../templates');

define('PHOTOS_URL', BASE_URL . '/assets/photos'); // URL publique
define('PHOTOS_DIR', __DIR__ . '/assets/photos'); // chemin disque (public/assets/photos)

$router = new Router($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

// main pages
$router->register(['GET'], '/', DashboardController::class, 'index');
$router->register(['GET', 'POST'], '/connection', LoginController::class, 'loginPage');
$router->register(['GET'], '/mon-profil', UserController::class, 'profile');
$router->register(['GET'], '/mes-covoiturages', UserController::class, 'listCarpools');
$router->register(['GET', 'POST'], '/covoiturages', CarpoolController::class, 'list');
$router->register(['GET'], '/mes-covoiturages/nouveau', CarpoolController::class, 'newCarpool');
$router->register(['GET'], '/mentions-legales', DashboardController::class, 'legalInformations');
$router->register(['GET'], '/contact', DashboardController::class, 'contact');
$router->register(['POST'], '/contact/send', DashboardController::class, 'sendContact');
$router->register(['GET'], '/espace-employe/valider-avis', UserController::class, 'employeeValidateRatings');
$router->register(['GET'], '/espace-employe/controler', UserController::class, 'employeeBadComments');
$router->register(['GET'], '/espace-admin/employes', UserController::class, 'adminEmployeeAccount');
$router->register(['GET'], '/espace-admin/utilisateurs', UserController::class, 'adminUserAccount');
$router->register(['GET'], '/espace-admin/statistiques', UserController::class, 'adminStatistics');


// login
$router->register(['POST'], '/login', LoginController::class, 'login');
$router->register(['POST'], '/newAccount', LoginController::class, 'newAccount');
$router->register(['POST'], '/deconnexion', LoginController::class, 'logout');
// userspace - action
$router->register(['POST'], '/mon-profil/update', UserController::class, 'editProfile');
$router->register(['POST'], '/mon-profil/photo', UserController::class, 'editPhoto');
$router->register(['POST'], '/car/add', CarController::class, 'new');
$router->register(['GET'], '/car/list', CarController::class, 'list');
$router->register(['GET'], '/car/select', CarController::class, 'select');
$router->register(['POST'], '/car/delete', CarController::class, 'delete');
$router->register(['POST'], '/preference/add', DriverController::class, 'newOtherPreference');
$router->register(['GET'], '/preference/list', DriverController::class, 'listOtherPreferences');
$router->register(['POST'], '/preference/update', DriverController::class, 'updateOtherPreference');
$router->register(['POST'], '/preference/delete', DriverController::class, 'deleteOtherPreference');
$router->register(['POST'], '/carpool/new', CarpoolController::class, 'new');

// carpools - action
$router->register(['GET'], '/covoiturages/details', CarpoolController::class, 'details');
$router->register(['POST'], '/reservation/check', ReservationController::class, 'checkParticipation');
$router->register(['POST'], '/reservation/update', ReservationController::class, 'updateParticipation');
$router->register(['POST'], '/carpool/approved', ReservationController::class, 'carpoolApproved');
$router->register(['POST'], '/carpool/rejected', ReservationController::class, 'carpoolRejected');
$router->register(['GET'], '/carpool/cancel', ReservationController::class, 'cancelCarpool');
$router->register(['GET'], '/carpool/start', ReservationController::class, 'startCarpool');
$router->register(['GET'], '/carpool/completed', ReservationController::class, 'completedCarpool');

// employee space
$router->register(['POST'], '/validate-rating', UserController::class, 'validateRating');
$router->register(['POST'], '/reject-rating', UserController::class, 'rejectRating');
$router->register(['POST'], '/resolve-bad-comment', UserController::class, 'resolveBadComment');

// admin space
$router->register(['POST'], '/suspend-employee', UserController::class, 'suspendEmployee');
$router->register(['POST'], '/reactivate-employee', UserController::class, 'reactivateEmployee');
$router->register(['POST'], '/new-employee', UserController::class, 'newEmployee');
$router->register(['POST'], '/suspend-user', UserController::class, 'suspendUser');
$router->register(['POST'], '/reactivate-user', UserController::class, 'reactivateUser');
$router->register(['GET'], '/chart-carpool-per-day', UserController::class, 'displayChartCarpoolPerDay');
$router->register(['GET'], '/chart-credits-earned', UserController::class, 'displayChartCreditsEarned');

echo $router->run();
