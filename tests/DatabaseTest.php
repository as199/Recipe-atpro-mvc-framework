<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Atpro\mvc\core\Database;

class DatabaseTest extends TestCase
{
    private string $testDbPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testDbPath = __DIR__ . '/database.sqlite';
        $_ENV['DRIVER'] = 'sqlite';
        $_ENV['DB_PATH'] = $this->testDbPath;
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testDbPath)) {
            unlink($this->testDbPath);
        }
        parent::tearDown();
    }

    public function testDatabaseConnection(): void
    {
        $db = Database::getInstance();
        $this->assertInstanceOf(Database::class, $db);
    }

    public function testSingletonInstance(): void
    {
        $db1 = Database::getInstance();
        $db2 = Database::getInstance();
        $this->assertSame($db1, $db2);
    }

    public function testDatabaseOperations(): void
    {
        $db = Database::getInstance();
        
        // Créer une table de test
        $db->exec('CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)');
        
        // Insérer des données
        $stmt = $db->prepare('INSERT INTO test (name) VALUES (?)');
        $stmt->execute(['Test Name']);
        
        // Vérifier l'insertion
        $result = $db->query('SELECT * FROM test')->fetch();
        $this->assertEquals('Test Name', $result->name);
    }
} 