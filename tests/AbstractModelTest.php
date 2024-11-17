<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Atpro\mvc\core\AbstractModel;

class TestModel extends AbstractModel
{
    protected array $fillable = ['name', 'email'];
    
    public function getDb()
    {
        return $this->db;
    }
}

class AbstractModelTest extends TestCase
{
    private TestModel $model;
    
    protected function setUp(): void
    {
        parent::setUp();
        $_ENV['DRIVER'] = 'sqlite';
        $_ENV['DB_PATH'] = ':memory:';
        
        $this->model = new TestModel();
        
        $this->model->getDb()->exec('DROP TABLE IF EXISTS test_model');
        
        $this->model->getDb()->exec('
            CREATE TABLE test_model (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT,
                email TEXT
            )
        ');
    }

    protected function tearDown(): void
    {
        $this->model->getDb()->exec('DROP TABLE IF EXISTS test_model');
        parent::tearDown();
    }

    public function testCreate(): void
    {
        $id = $this->model->create([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
        
        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);
    }

    public function testFind(): void
    {
        $id = $this->model->create([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
        
        $result = $this->model->find($id);
        $this->assertEquals('John Doe', $result->name);
    }

    public function testUpdate(): void
    {
        // Créer d'abord un enregistrement
        $id = $this->model->create([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
        
        // Récupérer le modèle
        $model = $this->model->find($id);
        
        
        // Sauvegarder les modifications
        $result = $model->update($id, [
            'name' => 'Jane Doe'
        ]);
        
        $this->assertTrue($result);
        $this->assertEquals('Jane Doe', $this->model->find($id)->name);
    }

    public function testDelete(): void
    {
        $id = $this->model->create([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
        
        $result = $this->model->delete($id);
        $this->assertTrue($result);
        $this->assertNull($this->model->find($id));
    }

    public function testPaginate(): void
    {
        // Créer 20 enregistrements
        for ($i = 1; $i <= 20; $i++) {
            $this->model->create([
                'name' => "User $i",
                'email' => "user$i@example.com"
            ]);
        }
        
        $result = $this->model->paginate(2, 5);
        
        $this->assertCount(5, $result['data']);
        $this->assertEquals(20, $result['total']);
        $this->assertEquals(2, $result['current_page']);
        $this->assertEquals(4, $result['last_page']);
    }
} 