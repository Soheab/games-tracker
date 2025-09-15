<?php

class Database {
    private PDO $conn;

    public function __construct(
        $host = "localhost",
        $dbname = "game_tracker",
        $user = "root",
        $pass = ""
    ) {
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connectie mislukt: " . $e->getMessage());
        }
    }

    public function getConnection(): PDO {
        return $this->conn;
    }

    public function lastInsertId(): string {
        return $this->conn->lastInsertId();
    }

    public function execute(string $query, array $params = []): array {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


