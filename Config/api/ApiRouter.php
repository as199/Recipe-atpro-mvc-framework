<?php

namespace Atpro\mvc\Config\api;

use JsonException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 */
class ApiRouter
{

    private string $url;
    private array $routes = [];

    public function __construct(string $url)
    {
        $this->url = $this->normalizeUrl($url);
    }

    /**
     * @author ASSANE DIONE <atpro0290@gmail.com>
     * @param string $path
     * @param string $action
     */
    public function get(string $path, string $action): void
    {
        $this->addRoute('GET', $path, $action);
    }

    /**
     * @author ASSANE DIONE <atpro0290@gmail.com>
     * @param string $path
     * @param string $action
     */
    public function post(string $path, string $action): void
    {
        $this->addRoute('POST', $path, $action);
    }

    /**
     * @author ASSANE DIONE <atpro0290@gmail.com>
     * @param string $path
     * @param string $action
     */
    public function put(string $path, string $action): void
    {
        $this->addRoute('PUT', $path, $action);
    }

    /**
     * @author ASSANE DIONE <atpro0290@gmail.com>
     * @param string $path
     * @param string $action
     */
    public function delete(string $path, string $action): void
    {
        $this->addRoute('DELETE', $path, $action);
    }

    /**
     * @author ASSANE DIONE <atpro0290@gmail.com>
     * @throws JsonException
     */
    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if (!isset($this->routes[$method])) {
            $this->sendJsonResponse(['error' => 'Method not allowed'], Response::HTTP_METHOD_NOT_ALLOWED);
            return;
        }

        foreach ($this->routes[$method] as $route) {
            if ($route->matches($this->url)) {
                $route->execute();
                return;
            }
        }

        $this->sendJsonResponse(['error' => 'Route not found'], Response::HTTP_NOT_FOUND);
    }

    private function addRoute(string $method, string $path, string $action): void
    {
        $this->routes[$method][] = new ApiRoute($path, $action);
    }

    private function normalizeUrl(string $url): string
    {
        return trim(filter_var($url, FILTER_SANITIZE_URL), '/');
    }

    /**
     * @throws JsonException
     */
    private function sendJsonResponse(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_THROW_ON_ERROR);
    }
}
