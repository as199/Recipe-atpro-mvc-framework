<?php

namespace Atpro\mvc\Config\web;

use Atpro\mvc\Config\services\AtproDenied;

/**
 * @author Assane Dione <atpro0290@gmail.com>
 */
class WebRouter
{

    public string $url;
    public array $routes = [];

    public function __construct($url)
    {
        $this->url = trim(filter_var($url, FILTER_SANITIZE_URL), DIRECTORY_SEPARATOR);
    }

    /**
     * @author Assane Dione <atpro0290@gmail.com>
     * @param string $path
     * @param string $action
     * @param array|null $middlewares
     */
    public function get(string $path, string $action, ?array $middlewares = []): void
    {
        $this->routes['GET'][] = new WebRoute($path, $action, $middlewares);
    }

    /**
     * @author Assane Dione <atpro0290@gmail.com>
     * @param string $path
     * @param string $action
     * @param array|null $middlewares
     */
    public function post(string $path, string $action, ?array $middlewares = []): void
    {
        $this->routes['POST'][] = new WebRoute($path, $action, $middlewares);
    }

    public function run()
    {
        foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $route) {
            if ($route->matches($this->url)) {
                return $route->execute();
            }
        }
        $denied = new AtproDenied();
        $denied->notFound();
    }
}
