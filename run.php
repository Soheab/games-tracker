<?php
include_once 'includes/db.php';
include_once 'includes/game.php';

class Runtime {
    public Database $db;

    // game_id => Game
    /** @var array<int, Game> */
    private array $games = [];

    // user_id => [collection_id => Collection, ...]
    /** @var array<int, array<Collection>> */
    private array $collections = [];

    // user_id => User
    /** @var array<int, User> */
    private array $users = [];

    public function __construct() {
        session_start();
        self::$db = new Database();
    }

    public function getGames(): array {
        if (empty($this->games)) {
            $query = "SELECT * FROM games ORDER BY created_at DESC";
            $results = $this->db->execute($query);
            foreach ($results as $row) {
                $this->games[$row['id']] = Game::fromArray($this, $row);
            }
        }
        return $this->games;
    }

    public function getCollections(): array {
        if (empty($this->collections)) {
            $query = "SELECT * FROM collections ORDER BY created_at DESC";
            $results = $this->db->execute($query);
            foreach ($results as $row) {
                $this->collections[$row['id']] = Collection::fromArray($this, $row);
            }
        }
        return $this->collections;
    }

    public function getUsers(): array {
        if (empty($this->users)) {
            $query = "SELECT * FROM users ORDER BY created_at DESC";
            $results = $this->db->execute($query);
            foreach ($results as $row) {
                $this->users[$row['id']] = User::fromArray($this, $row);
            }
        }
        return $this->users;
    }

    public function addGame(Game $game): void {
        $this->games[$game->id] = $game;
    }

    public function addCollection(int $userId, Collection $collection): void {
        if (!isset($this->collections[$userId])) {
            $this->collections[$userId] = [];
        }
        array_push($this->collections[$userId], $collection);
    }

    public function addUser(User $user): void {
        $this->users[$user->id] = $user;
    }

    public function getUser(int $userId): ?User {
        $cached = $this->users[$userId] ?? null;
        if ($cached !== null) {
            return $cached;
        }
        $query = "SELECT * FROM users WHERE id = :user_id";
        $params = ['user_id' => $userId];
        $results = $this->db->execute($query, $params);
        if (empty($results)) {
            return null;
        }
        $user = User::fromArray($this, $results[0]);
        $this->addUser($user);
        return $user;
    }

    public function getGame(int $id): ?Game {
        $cached = $this->games[$id] ?? null;
        if ($cached !== null) {
            return $cached;
        }
        $query = "SELECT * FROM games WHERE id = :game_id";
        $params = ['game_id' => $id];
        $results = $this->db->execute($query, $params);
        if (empty($results)) {
            return null;
        }

        $game = Game::fromArray($this, $results[0]);
        $this->addGame($game);
        return $game;
    }

    public function getCollection(int $userId, int $collectionId): ?Collection {
        $cached = $this->collections[$userId][$collectionId] ?? null;
        if ($cached !== null) {
            return $cached;
        }
        $query = "SELECT * FROM collections WHERE id = :collection_id AND user_id = :user_id";
        $params = ['collection_id' => $collectionId, 'user_id' => $userId];
        $results = $this->db->execute($query, $params);
        if (empty($results)) {
            return null;
        }
        $collection = Collection::fromArray($this, $results[0]);
        $this->addCollection($userId, $collection);
        return $collection;
    }

    public function getUserCollections(int $userId): array {
        $cached = $this->collections[$userId] ?? null;
        if ($cached !== null) {
            return $cached;
        }
        $query = "SELECT * FROM collections WHERE user_id = :user_id";
        $params = ['user_id' => $userId];
        $results = $this->db->execute($query, $params);
        if (empty($results)) {
            return [];
        }
        $this->collections[$userId] = [];
        foreach ($results as $row) {
            $collection = Collection::fromArray($this, $row);
            $this->addCollection($userId, $collection);
        }
        return $this->collections[$userId];
    }
}
