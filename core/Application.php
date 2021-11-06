<?php

namespace Atpro\mvc\core;

use Atpro\mvc\Config\api\ApiRouter;
use Atpro\mvc\Config\web\WebRouter;
use JsonException;

class Application
{
    /**
     * @author Assane Dione <atpro0290@gmail.com>
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
                try {
                    $apiRouter->run();
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
                
            }
            try {
                    $webRouter->run();
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
            
        }
    }

}