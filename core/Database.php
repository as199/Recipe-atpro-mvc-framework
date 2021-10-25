<?php

namespace Atpro\mvc\core;

use PDO;
use PDOException;

class Database extends PDO
{
    protected  $instance;

    public function __construct()
    {
        if (strtolower($_ENV['DRIVER']) === 'mysql') {
            $_dsn = 'mysql:host='.$_ENV['DB_HOST'] .';dbname='.$_ENV['DB_NAME'];
            try {
                parent::__construct($_dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
                $this->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');
                $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die($e->getMessage());
            }
        } else {
            $_dsn = 'pgsql:host='.$_ENV["DB_HOST"].';
            port='.$_ENV["DB_PORT"].';dbname='.$_ENV["DB_NAME"].';
            user='.$_ENV['DB_USER'].';
            password='.$_ENV['DB_PASSWORD'];
            try {
                parent::__construct($_dsn);
                $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die($e->getMessage());
            }
        }
    }

    /**
     * @return Database
     */
    protected function getInstance(): Database
    {
        if ($this->instance === null) {
            $this->instance = new Database();
        }
        return $this->instance;
    }
}
