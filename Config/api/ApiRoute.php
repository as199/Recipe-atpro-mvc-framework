<?php

namespace Atpro\mvc\Config\api;

use JsonException;

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 */
class ApiRoute
{
    public string $path;
    public string $action;
    public $matchs;

    public function __construct($path, $action)
    {
        $pathBrute = explode(DIRECTORY_SEPARATOR, trim($path, DIRECTORY_SEPARATOR));
        unset($pathBrute[0]);
        $this->path  = trim(implode(DIRECTORY_SEPARATOR, $pathBrute), DIRECTORY_SEPARATOR);
        $this->action = $action;
    }
    /**
     * @author ASSANE DIONE <atpro0290@gmail.com>
     * @param string $url
     * @return boolean
     */
    public function matches(string $url): bool
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
     * @throws JsonException
     */
    public function execute()
    {
        $params = explode('@', $this->action);
        $controllerName= 'App\ApiController\\'.$params[0];
        $controller = new $controllerName();
        $method = $params[1];
        if (method_exists($controller, $method)) {
            $result = empty($controller->getAccess()) || VerifierAccessApi($controller->getAccess(), $method);
            if ($result) {
                return (isset($this->matchs[1])) ? $controller->$method($this->matchs[1]) : $controller->$method();
            }

            http_response_code(401);
            Json_response(['message' => 'Access not autoriser']);
        }
        http_response_code(500);
        Json_response(['message' => 'cette routes nexiste pas']);
    }
}
