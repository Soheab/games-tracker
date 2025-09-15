<?php

class DatabaseHandler {
    protected static string $tableName;
    protected Runtime $__runtime;

    public ?int $id;
    
    public function __construct(Runtime $runtime, ?int $id) {
        $this->__runtime = $runtime;
        $this->id = $id;
    }

    public static function fromArray(Runtime $runtime, array $data): static {
        return new static($runtime, ...array_values($data));
    }
    
    public function toArray(): array {
        return get_object_vars($this);
    }

    public static function getOne(Runtime $runtime, int $id): static {
        $query = "SELECT * FROM " . static::$tableName . " WHERE id = :id";
        $params = ['id' => $id];
        $result = $runtime->db->execute($query, $params);
        if (count($result) === 0) {
            throw new Exception(static::$tableName . " met id $id niet gevonden.");
        }
        return static::fromArray($runtime, $result[0]);
    }

    public static function getAll(Runtime $runtime): array {
        $query = "SELECT * FROM " . static::$tableName;
        $results = $runtime->db->execute($query);
        $objects = [];
        foreach ($results as $row) {
            $objects[] = static::fromArray($runtime, $row);
        }
        return $objects;
    }

    public function store(): self {
        if ($this->id === null) {
            $properties = $this->toArray();
            unset($properties['__runtime'], $properties['id']);
            
            $columns = implode(', ', array_keys($properties));
            $placeholders = ':' . implode(', :', array_keys($properties));
            
            $query = "INSERT INTO " . static::$tableName . " ($columns) VALUES ($placeholders)";
            $this->__runtime->db->execute($query, $properties);
            $this->id = (int)$this->__runtime->db->lastInsertId();
        } else {
            $properties = $this->toArray();
            unset($properties['__runtime'], $properties['id']);
            
            $setClause = implode(', ', array_map(fn($key) => "$key = :$key", array_keys($properties)));
            
            $query = "UPDATE " . static::$tableName . " SET $setClause WHERE id = :id";
            $properties['id'] = $this->id;
            $this->__runtime->db->execute($query, $properties);
        }
        return $this;
    }

    public function delete(): bool {
        if ($this->id === null) {
            return false;
        }
        $query = "DELETE FROM " . static::$tableName . " WHERE id = :id";
        $params = ['id' => $this->id];
        $this->__runtime->db->execute($query, $params);
        return true;
    }
}

