<?php

namespace Atpro\mvc\core;

use Atpro\mvc\Config\api\ApiRouter;
use Atpro\mvc\Config\web\WebRouter;
use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Application
{
    private static ?self $instance = null;
    private Request $request;
    private array $config = [];

    private function __construct()
    {
        $this->request = Request::createFromGlobals();
        $this->loadEnvironment();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @throws JsonException
     */
    public static function start(string $webRoute, string $apiRoute): void
    {
        $app = self::getInstance();
        
        if (PHP_SAPI === 'cli') {
            return;
        }

        try {
            if ($app->isApiRequest()) {
                $app->handleApiRequest($apiRoute);
            } else {
                $app->handleWebRequest($webRoute);
            }
        } catch (\Throwable $e) {
            $app->handleError($e);
        }
    }

    private function handleApiRequest(string $apiRoute): void
    {
        $router = new ApiRouter($this->request->getPathInfo());
        require $apiRoute;
        $router->run();
    }

    private function handleWebRequest(string $webRoute): void
    {
        $router = new WebRouter($this->request->getPathInfo());
        require $webRoute;
        $router->run();
    }

    private function handleError(\Throwable $e): void
    {
        if ($this->isApiRequest()) {
            $this->sendJsonError($e);
        } else {
            $this->sendHtmlError($e);
        }
    }

    private function sendJsonError(\Throwable $e): void
    {
        $statusCode = $e instanceof HttpException ? $e->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
        
        header('Content-Type: application/json');
        http_response_code($statusCode);
        
        echo json_encode([
            'error' => true,
            'message' => $this->config['debug'] ? $e->getMessage() : 'Internal Server Error',
            'code' => $e->getCode()
        ], JSON_THROW_ON_ERROR);
    }

    private function sendHtmlError(\Throwable $e): void
    {
        if ($this->config['debug']) {
            throw $e;
        }
        
        http_response_code(Response::HTTP_INTERNAL_SERVER_ERROR);
        include '../views/errors/500.php';
    }

    private function isApiRequest(): bool
    {
        return str_starts_with($this->request->getPathInfo(), '/api/') 
            || $this->request->headers->get('Accept') === 'application/json';
    }

    private function loadEnvironment(): void
    {
        $this->config = [
            'debug' => $_ENV['APP_ENV'] === 'dev',
            'timezone' => $_ENV['APP_TIMEZONE'] ?? 'UTC',
        ];
        
        date_default_timezone_set($this->config['timezone']);
    }

    private function __clone() {}
}