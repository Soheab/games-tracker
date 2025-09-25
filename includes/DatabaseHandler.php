<?php

namespace App;

use Exception;
use RuntimeException;

class DatabaseHandler
{
    protected static string $tableName;
    public ?int $id;
    protected Runtime $runtime;

    public function __construct(Runtime $runtime, ?int $id)
    {
        $this->runtime = $runtime;
        $this->id = $id;
    }

    /**
     * @throws Exception
     */
    public static function getOne(Runtime $runtime, int $id): static
    {
        $query = "SELECT * FROM ".static::$tableName." WHERE id = :id";
        $params = ['id' => $id];
        $result = $runtime->db->execute($query, $params);
        if (count($result) === 0) {
            throw new RuntimeException(static::$tableName." met id $id niet gevonden.");
        }

        return static::fromArray($runtime, $result[0]);
    }

    public static function fromArray(Runtime $runtime, array $data): static
    {
        $data = static::handleArray($data);
        $existingArray = get_class_vars(static::class);
        $filteredData = array_filter(
            $data,
            static fn ($key) => array_key_exists($key, $existingArray),
            ARRAY_FILTER_USE_KEY
        );

        return new static(
            $runtime,
            $filteredData['id'] ?? null,
            ...array_values(array_filter($filteredData, static fn ($key) => $key !== 'id', ARRAY_FILTER_USE_KEY))
        );
    }

    public static function handleArray(array $data): array
    {
        return $data;
    }

    public static function getAll(Runtime $runtime): array
    {
        $query = "SELECT * FROM ".static::$tableName;
        $results = $runtime->db->execute($query);
        $objects = [];
        foreach ($results as $row) {
            $objects[] = static::fromArray($runtime, $row);
        }

        return $objects;
    }

    public function store(): self
    {
        if ($this->id === null) {
            $properties = $this->toArray();
            unset($properties['runtime'], $properties['id']);

            $columns = implode(', ', array_keys($properties));
            $placeholders = ':'.implode(', :', array_keys($properties));

            $query = "INSERT INTO ".static::$tableName." ($columns) VALUES ($placeholders)";
            $this->runtime->db->execute($query, $properties);
            $this->id = (int)$this->runtime->db->lastInsertId();
        } else {
            $properties = $this->toArray();
            unset($properties['runtime'], $properties['id']);

            $setClause = implode(', ', array_map(static fn ($key) => "$key = :$key", array_keys($properties)));

            $query = "UPDATE ".static::$tableName." SET $setClause WHERE id = :id";
            $properties['id'] = $this->id;
            $this->runtime->db->execute($query, $properties);
        }

        return $this;
    }

    public function toArray(): array
    {
        throw new RuntimeException('Methode toArray() moet worden overschreven in de subklasse.');
    }

    public function delete(): bool
    {
        if ($this->id === null) {
            return false;
        }
        $query = "DELETE FROM ".static::$tableName." WHERE id = :id";
        $params = ['id' => $this->id];
        $this->runtime->db->execute($query, $params);

        return true;
    }
}
