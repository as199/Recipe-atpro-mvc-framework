<?php

namespace Atpro\mvc\Config\web;

use Atpro\Config\services\AtproDenied;

class WebRoute
{
    public string $path;
    public string $action;
    public array $matchs;
    public array $middlewares;
    public AtproDenied $denied;

    /**
     * @param $path
     * @param $action
     * @param array $middlewares
     */
    public function __construct($path, $action, array $middlewares = [])
    {
        $this->path = trim($path, DIRECTORY_SEPARATOR);
        $this->action = $action;
        $this->middlewares = $middlewares;
        $this->denied = new AtproDenied();
    }

    public function matches(string $url): ?bool
    {
        $path = preg_replace('#:([\w]+)#', '([^/]+)', $this->path);
        $pathToMatch = "#^$path$#";

        if (preg_match($pathToMatch, $url, $matches)) {
            $this->matchs = $matches;
            return true;
        }

        return false;
    }

    public function execute()
    {
        $params = explode('@', $this->action);
        $controller = new $params[0]();
        $method = $params[1];
        if (!empty($this->middlewares)) {
            foreach ($this->middlewares as $middle) {
                $middleware = 'GlobalHelpers\\middlewares\\'.ucfirst(strtolower($middle)).'Middleware';
                (new $middleware())->execute();
            }
        }
        if (method_exists($controller, $method)) {
            $result = empty($controller->getAccess()) || VerifierAccess($controller->getAccess(), $method);
            if ($result) {
                return (isset($this->matchs[1])) ? $controller->$method($this->matchs[1]) : $controller->$method();
            }
            $this->denied->denied();
            exit();
        }
        http_response_code(404);
        $this->denied->notFound();
    }
}
