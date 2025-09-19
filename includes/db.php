<?php

class Database
{
    private PDO $conn;

    public function __construct(
        $host = "localhost",
        $dbname = "games_tracker",
        $user = "root",
        $pass = ""
    ) {
        try {
            $this->conn = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8",
                $user,
                $pass,
                [PDO::ATTR_PERSISTENT => true] // Persistent connection
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connectie mislukt: " . $e->getMessage());
        }
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

    public function fetchAll(string $query, array $params = []): array
    {
        $stmt = $this->execute($query, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
    }


    public function execute(string $query, array $params = []): PDOStatement
    {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }
}
