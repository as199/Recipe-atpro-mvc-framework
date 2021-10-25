<?php

namespace Atpro\Config\services;

use Atpro\mvc\core\AbstractController;

class AtproDenied extends AbstractController
{
    public function denied($smg = '')
    {
        header('HTTP/1.0 401 Unauthorized ');
        return $this->render('denied/denied', ['sms' => $smg]);
    }

    public function notFound()
    {
        header('HTTP/1.0 404 Not Found ');
        return $this->render('404/404', []);
    }

    public function getAccess(): array
    {
        return [];
    }
}
