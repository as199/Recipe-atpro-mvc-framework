<?php

namespace Atpro\mvc\Config\services;

use Exception;
use Atpro\mvc\core\AbstractController;

class AtproDenied extends AbstractController
{
    /**
     * @author Assane Dione <atpro0290@gmail.com>
     *
     * @param string $smg
     * @return void
     */
    public function denied($smg = '')
    {
        header('HTTP/1.0 401 Unauthorized ');
        return throw new Exception("Access Denied");
    }

    /**
     *@author Assane Dione <atpro0290@gmail.com>
     *
     * @return void
     */
    public function notFound()
    {
        header('HTTP/1.0 404 Not Found ');
        return throw new Exception("404 page not found");
    }

    public function getAccess(): array
    {
        return [];
    }
}
