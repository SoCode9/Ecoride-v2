<?php
//17.09.2025 _ Live découverte PHP Objet 4/6, 5/6 et 6/6 router

namespace App\Routing;

class Router
{
    private array $routes = [];
    private ?string $template = null;

    public function __construct(private string $method, private string $uri) {}
    public function register(string|array $method, string $uri, string $controller, string $action)
    {

        if (!is_array($method)) {
            $method = [$method]; //se transforme en tableau
        }

        $uri = $this->normalizePath($uri);

        $this->routes[$uri] = [ //la route s'enregistre avec l'uri en clé
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function run()
    {
        $path = $this->normalizePath($this->uri);
        $routeInfo = $this->routes[$path];

        /*  echo '<pre>Informations sur la route : <pre>';
        print_r($routeInfo);
        echo '</pre>'; */

        $controller = $routeInfo['controller'];
        $action = $routeInfo['action'];

        if (!class_exists($controller)) {
            throw new \LogicException('Le controller ' . $controller . ' n\'existe pas.');
        }

        $controller = new $controller($this);


        if (!method_exists($controller, $action)) {
            throw new \LogicException('La méthode ' . $action . ' n\'existe pas dans le controller ' . $controller::class);
        }

        if (!in_array($this->method, $routeInfo['method'])) {
            throw new \Exception($this->method . ' n\'est pas autorisée pour cette URL');
        }

   //     die($this->getCurrentPage());

        return $controller->$action(); //ici va dans méthode du controller list p.ex
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getCurrentPage()
    {
        return $this->normalizePath($this->uri);
    }

    /** Normalise un chemin: enlève le base path (/2coursPhp/public), la query string, gère les slashs. */
    private function normalizePath(string $uri): string
    {
        // 1) garder seulement le path (sans ?query)
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        // 2) détecter le "base path" depuis SCRIPT_NAME = /2coursPhp/public/index.php
        $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/'); // => /2coursPhp/public
        if ($scriptDir && $scriptDir !== '/' && str_starts_with($path, $scriptDir)) {
            $path = substr($path, strlen($scriptDir));
        }

        // 3) normaliser les slashs
        $path = '/' . ltrim($path, '/');
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        return $path;
    }
}
