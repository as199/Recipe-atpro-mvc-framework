<?php

namespace Atpro\mvc\Config\api;

class ApiRouter
{

    public string $url;
    public array $routes = [];

    public function __construct($url)
    {
        $urlBrute = explode(DIRECTORY_SEPARATOR, trim(filter_var($url, FILTER_SANITIZE_URL), DIRECTORY_SEPARATOR));
        unset($urlBrute[0]);
        $this->url = trim(implode(DIRECTORY_SEPARATOR, $urlBrute), DIRECTORY_SEPARATOR);
    }

    /**
     * @param string $path
     * @param string $action
     */
    public function get(string $path, string $action): void
    {
        $this->routes['GET'][] = new ApiRoute($path, $action);
    }

    /**
     * @param string $path
     * @param string $action
     */
    public function post(string $path, string $action): void
    {
        $this->routes['POST'][] = new ApiRoute($path, $action);
    }

    /**
     * @param string $path
     * @param string $action
     */
    public function delete(string $path, string $action): void
    {
        $this->routes['DELETE'][] = new ApiRoute($path, $action);
    }
    /**
     * @param string $path
     * @param string $action
     */
    public function patch(string $path, string $action): void
    {
        $this->routes['PATCH'][] = new ApiRoute($path, $action);
    }

    /**
     * @param string $path
     * @param string $action
     */
    public function put(string $path, string $action): void
    {
        $this->routes['PUT'][] = new ApiRoute($path, $action);
    }

    public function run()
    {
        if (!isset($this->routes[$_SERVER['REQUEST_METHOD']])) {
            http_response_code(500);
            Json_response([
                'message' => "Method ".strtolower($_SERVER['REQUEST_METHOD'])." not found on ApiRoutes.php"
            ]);
            exit();
        }
        foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $route) {
            if ($route->matches($this->url)) {
                return $route->execute();
            }
        }
        http_response_code(400);
        Json_response(['message' => 'Bad request']);
    }
}
