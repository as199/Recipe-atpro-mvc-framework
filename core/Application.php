<?php

namespace Atpro\mvc\core;

use Atpro\mvc\Config\api\ApiRouter;
use Atpro\mvc\Config\web\WebRouter;
use JsonException;

class Application
{
    /**
     * @throws JsonException
     */
    public static function start()
    {
        $webRouter = new WebRouter(GLOBAL_URL);
        $apiRouter = new ApiRouter(GLOBAL_URL);

        include(dirname(__DIR__) .WEB);
        include(dirname(__DIR__) .API);

        if (PHP_SAPI !== 'cli') {
            if (isAPI()) {
                $apiRouter->run();
            }
            $webRouter->run();
        }
    }

}