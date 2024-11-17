<?php

namespace Atpro\mvc\core;

use PDO;
use PDOStatement;
use RuntimeException;

abstract class AbstractModel
{
    protected PDO $db;
    protected string $table;
    protected ?int $id = null;
    protected array $fillable = [];
    protected array $hidden = ['password'];

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->table = $this->getTableName();
        $this->initializeFillable();
    }

    protected function execute(string $sql, array $params = []): PDOStatement
    {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (\PDOException $e) {
            throw new RuntimeException("Database error: " . $e->getMessage());
        }
    }

    public function findAll(array $columns = ['*']): array
    {
        $cols = implode(', ', $columns);
        return $this->execute("SELECT {$cols} FROM {$this->table}")->fetchAll();
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->getTable() . " WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$result) {
            return null;
        }
        
        // Créer une nouvelle instance du modèle avec les données
        $model = new static();
        foreach ($result as $key => $value) {
            $model->$key = $value;
        }
        return $model;
    }

    public function findBy(array $criteria, array $columns = ['*']): array
    {
        $cols = implode(', ', $columns);
        $where = implode(' AND ', array_map(fn($field) => "$field = ?", array_keys($criteria)));
        
        return $this->execute(
            "SELECT {$cols} FROM {$this->table} WHERE {$where}",
            array_values($criteria)
        )->fetchAll();
    }

    public function create(array $data): ?int
    {
        $fields = array_intersect_key($data, array_flip($this->fillable));
        
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', array_keys($fields)),
            str_repeat('?,', count($fields) - 1) . '?'
        );

        $this->execute($sql, array_values($fields));
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = array_intersect_key($data, array_flip($this->fillable));
        if (empty($fields)) {
            return false;
        }

        $sql = sprintf(
            "UPDATE %s SET %s WHERE id = ?",
            $this->table,
            implode(', ', array_map(fn($field) => "$field = ?", array_keys($fields)))
        );

        $values = array_values($fields);
        $values[] = $id;

        return $this->execute($sql, $values)->rowCount() === 1;
    }

    public function delete(int $id): bool
    {
        return $this->execute(
            "DELETE FROM {$this->table} WHERE id = ?",
            [$id]
        )->rowCount() === 1;
    }

    public function paginate(int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        $total = (int)$this->execute("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
        
        $items = $this->execute(
            "SELECT * FROM {$this->table} LIMIT ? OFFSET ?",
            [$perPage, $offset]
        )->fetchAll();

        return [
            'data' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }

    protected function getModifiableFields(): array
    {
        $fields = [];
        foreach (get_object_vars($this) as $field => $value) {
            if (in_array($field, $this->fillable) && !is_null($value)) {
                $fields[$field] = $value;
            }
        }
        return $fields;
    }

    protected function getTableName(): string
    {
        $className = (new \ReflectionClass($this))->getShortName();
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className));
    }

    protected function initializeFillable(): void
    {
        if (empty($this->fillable)) {
            $properties = (new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PUBLIC);
            $this->fillable = array_map(fn($prop) => $prop->getName(), $properties);
        }
    }

    public function toArray(): array
    {
        $data = get_object_vars($this);
        return array_diff_key($data, array_flip($this->hidden));
    }

    public function getDb()
    {
        return $this->db;
    }

    public function getTable(): string
    {
        return $this->table;
    }
}
