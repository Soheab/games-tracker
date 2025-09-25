<?php

namespace App;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private PDO $conn;
    private string $host;
    private string $dbname;
    private string $user;
    private string $pass;

    public function __construct(
        $host = "localhost",
        $dbname = "games_tracker",
        $user = "root",
        $pass = ""
    ) {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->user = $user;
        $this->pass = $pass;
        $this->connect();
    }

    private function connect(): void
    {
        $this->conn = new PDO(
            "mysql:host={$this->host};dbname={$this->dbname};charset=utf8",
            $this->user,
            $this->pass,
        );
        try {
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connectie mislukt: " . $e->getMessage());
        }
    }

    public function __sleep(): array
    {
        // Exclude $conn from serialization
        return ['host', 'dbname', 'user', 'pass'];
    }

    public function __wakeup(): void
    {
        $this->connect();
    }

    public function lastInsertId(): string
    {
        return $this->conn->lastInsertId();
    }

    public function fetchOne(string $query, array $params = []): array|null
    {
        $stmt = $this->execute($query, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function execute(string $query, array $params = []): PDOStatement
    {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchAll(string $query, array $params = []): array
    {
        $stmt = $this->execute($query, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
    }
}
