<?php

namespace Atpro\mvc\core;

use PDO;
use PDOException;

/**
 * @author ASSANE DIONE <atpro0290@gmail.com>
 */
class Database extends PDO
{
    private static ?self $instance = null;
    private array $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_EMULATE_PREPARES => false
    ];

    /**
     * @author ASSANE DIONE <atpro0290@gmail.com>
     */
    public function __construct()
    {
        $dsn = $this->buildDsn();
        
        try {
            parent::__construct(
                $dsn, 
                $_ENV['DRIVER'] !== 'sqlite' ? $_ENV['DB_USER'] : null,
                $_ENV['DRIVER'] !== 'sqlite' ? $_ENV['DB_PASSWORD'] : null,
                $this->options
            );
            
            if (strtolower($_ENV['DRIVER']) === 'mysql') {
                $this->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8mb4');
            }
        } catch (PDOException $e) {
            throw new PDOException("Erreur de connexion : " . $e->getMessage());
        }
    }

    /**
     * @author ASSANE DIONE <atpro0290@gmail.com>
     * @return Database
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function buildDsn(): string 
    {
        return match (strtolower($_ENV['DRIVER'])) {
            'mysql' => sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8mb4',
                $_ENV['DB_HOST'],
                $_ENV['DB_NAME']
            ),
            'pgsql' => sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                $_ENV['DB_HOST'],
                $_ENV['DB_PORT'],
                $_ENV['DB_NAME']
            ),
            'sqlite' => sprintf(
                'sqlite:%s',
                $_ENV['DB_PATH'] ?? __DIR__ . '/../../database/database.sqlite'
            ),
            default => throw new PDOException("Driver de base de données non supporté")
        };
    }

    /**
     * Crée le fichier SQLite s'il n'existe pas
     */
    private function initializeSqliteFile(): void
    {
        if ($_ENV['DRIVER'] === 'sqlite') {
            $dbPath = $_ENV['DB_PATH'] ?? __DIR__ . '/../../database/database.sqlite';
            $dbDir = dirname($dbPath);
            
            if (!file_exists($dbDir)) {
                mkdir($dbDir, 0777, true);
            }
            
            if (!file_exists($dbPath)) {
                touch($dbPath);
                chmod($dbPath, 0777);
            }
        }
    }

    private function __clone() {} // Empêche le clonage
}
