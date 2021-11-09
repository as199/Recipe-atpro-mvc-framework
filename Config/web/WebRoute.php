<?php

namespace Atpro\mvc\Config\web;


use Atpro\mvc\Config\services\AtproDenied;

/**
 * @author Assane Dione <atpro0290@gmail.com>
 */
class WebRoute
{
    public string $path;
    public string $action;
    public array $matchs;
    public array $middlewares;
    public AtproDenied $denied;
    /**
     * @author ASSANE DIONE <atpro0290@gmail.com>
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
    /**
     * @author ASSANE DIONE <atpro0290@gmail.com>
     *
     * @param string $url
     * @return boolean|null
     */
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
    /**
     * @author ASSANE DIONE <atpro0290@gmail.com>
     * @return void
     */
    public function execute()
    {
<<<<<<< HEAD
        $params = explode('@', $this->action);
        $controllerName ='App\WebController\\'.$params[0];
        $controller = new $controllerName();
=======
        $params = explode("@", $this->action);
        $controller = new $params[0]();
>>>>>>> b42d3956a25b59232a89859829999abcd59c3269
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
        exit();
    }
}
