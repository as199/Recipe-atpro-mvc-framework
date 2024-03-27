<?php

namespace GlobalHelpers\middlewares;

use Atpro\Config\services\AtproDenied;
use Exception;

class AuthMiddleware extends BaseMiddleware
{
    private AtproDenied $denied;

    public function __construct()
    {
        $this->denied = new AtproDenied();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function execute():void
    {
        if (!isAuth()) {
            $this->denied->denied();
            exit();
        }
    }
}
