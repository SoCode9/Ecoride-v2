<?php

//17.09.2025 _ Live découverte PHP Objet 4/6 router

require_once __DIR__ . '/../vendor/autoload.php';

use App\Carpool\Controller\CarpoolController;
use App\Dashboard\DashboardController;
use App\User\Controller\UserController;
use Symfony\Component\Dotenv\Dotenv;
use App\Routing\Router;

//charge configuration
$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

// variable vers dossier assets dans public
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'); //enlève la dernière partie (index.php) pour ne garder que le dossier "/2coursPhp/public" 
define('BASE_URL', $baseUrl); // stocke /2coursPhp/public
define('ASSETS_PATH', BASE_URL . '/assets/');              // -> /2coursPhp/public/assets/

define('TEMPLATE_PATH', __DIR__.'/../templates/main.php');

$router = new Router($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']); //URI : tout ce qui est derrière le nom de domaine

// GET = méthode utilisée dans le navigateur
// /user/list = l'url dans le navigateur après l'host (on parle d'URI)
// MaClass = la classe à charger pour appeler le dernier paramètre
// listUser = la fonction (méthode) à appeler dans la classe MaClass
$router->register(['GET'], '/', DashboardController::class, 'index');
$router->register(['GET'], '/user/list', UserController::class, 'list');
$router->register('GET', '/carpool/list', CarpoolController::class, 'list');

// exemple d'une route pour créer un user
// $router->register(['GET', 'POST'], '/user/create', UserController::class, 'create');

/* echo '<pre>';
print_r($router->getRoutes());
echo '</pre>'; */

echo $router->run(); //va gérer l'affichage derrière
