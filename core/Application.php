<?php

namespace Atpro\mvc\core;

use Atpro\mvc\Config\api\ApiRouter;
use Atpro\mvc\Config\web\WebRouter;
use JsonException;

class Application
{
    /**
     * @throws JsonException
     * @param string $webRoute Routes web
     * @param string $apiRoute Routes api
     */
    public static function start( string $webRoute, string $apiRoute)
    {
        $webRouter = new WebRouter(GLOBAL_URL);
        $apiRouter = new ApiRouter(GLOBAL_URL);

        include($webRoute);
        include($apiRoute);

        if (PHP_SAPI !== 'cli') {
            if (isAPI()) {
                $apiRouter->run();
            }
            $webRouter->run();
        }
    }

}