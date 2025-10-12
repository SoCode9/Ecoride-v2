<?php

namespace App\Routing;

class Router
{
    private array $routes = [];
    public function __construct(private string $method, private string $uri) {}

    public function register(string|array $method, string $uri, string $controller, string $action)
    {

        if (!is_array($method)) {
            $method = [$method]; 
        }

        $uri = $this->normalizePath($uri);

        $this->routes[$uri] = [ // the route is registered with the URI as the key
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'action' => $action
        ];
    }

    /** Builds a URL for a given route + query parameters */
    public function generatePath(string $uri, array $params = []): string
    {
        $path = $this->normalizePath($uri);
        // Builds the query string from the parameter array.
        // Ex: ['id' => '42'] -> '?id=42'
        $qs   = $params ? ('?' . http_build_query($params)) : '';

        return BASE_URL . $path . $qs;
    }

    public function run()
    {
        $path = $this->normalizePath($this->uri);
        $routeInfo = $this->routes[$path];

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

        return $controller->$action(); // go into the controller list method, for example
    }

    public function getCurrentPage()
    {
        return $this->normalizePath($this->uri);
    }

    /** Normalizes a path: removes the base path, the query string, handles slashes. */
    private function normalizePath(string $uri): string
    {
        // 1) keep only the path
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        // 2) detect the “base path” from SCRIPT_NAME 
        $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/'); // => /2coursPhp/public
        if ($scriptDir && $scriptDir !== '/' && str_starts_with($path, $scriptDir)) {
            $path = substr($path, strlen($scriptDir));
        }

        // 3) normalize slashes
        $path = '/' . ltrim($path, '/');
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        return $path;
    }
}
