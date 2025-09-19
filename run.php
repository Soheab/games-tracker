<?php
include_once __DIR__ . '../includes/db.php';
include_once __DIR__ . '../includes/game-class.php';
include_once __DIR__ . '../includes/collection-class.php';
include_once __DIR__ . '../includes/user-class.php';

class Runtime
{
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

    // logged in user
    private ?User $user = null;

    public function __construct()
    {
        // Ensure session is started before any output
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['runtime'])) {
            $saved = $_SESSION['runtime'];
            $this->db = $saved->db;
            $this->games = $saved->games;
            $this->collections = $saved->collections;
            $this->users = $saved->users;
            return;
        }

        $_SESSION['runtime'] = $this;
        $this->db = new Database();
    }

    public function setCurrentUser(User $user): void
    {
        $this->user = $user;
    }

    public function getCurrentUser(): ?User
    {
        return $this->user;
    }

    public function isLoggedIn(): bool
    {
        return $this->user !== null;
    }

    public function login(string $email, string $password): bool
    {
        $query = "SELECT * FROM users WHERE email = :email";
        $params = ['email' => $email];
        $userPayload = $this->db->fetchOne($query, $params);
        if (!$userPayload) {
            throw new RuntimeException("Gebruiker niet gevonden. Heb je wel een account aangemaakt? <a href='create.php'>Registreer hier</a>.");
        }
        if (!password_verify($password, $userPayload['password_hash'])) {
            throw new RuntimeException("Ongeldig wachtwoord.");
        }
        $user = User::fromArray($this, $userPayload);
        $this->setCurrentUser($user);
        return true;
    }

    public function logout(): void
    {
        $this->user = null;
        session_destroy();
    }

    public function getGames(): array
    {
        if (empty($this->games)) {
            $query = "SELECT * FROM games ORDER BY created_at DESC";
            $results = $this->db->execute($query);
            foreach ($results as $row) {
                $this->games[$row['id']] = Game::fromArray($this, $row);
            }
        }
        return $this->games;
    }

    public function getCollections(): array
    {
        if (empty($this->collections)) {
            $query = "SELECT * FROM collections ORDER BY created_at DESC";
            $results = $this->db->execute($query);
            foreach ($results as $row) {
                $this->collections[$row['id']] = Collection::fromArray($this, $row);
            }
        }
        return $this->collections;
    }

    public function getUsers(): array
    {
        if (empty($this->users)) {
            $query = "SELECT * FROM users ORDER BY created_at DESC";
            $results = $this->db->execute($query);
            foreach ($results as $row) {
                $this->users[$row['id']] = User::fromArray($this, $row);
            }
        }
        return $this->users;
    }

    public function addGame(Game $game): void
    {
        $this->games[$game->id] = $game;
    }

    public function addCollection(int $userId, Collection $collection): void
    {
        if (!isset($this->collections[$userId])) {
            $this->collections[$userId] = [];
        }
        array_push($this->collections[$userId], $collection);
    }

    public function addUser(User $user): void
    {
        $this->users[$user->id] = $user;
    }

    public function getUser(int $userId): ?User
    {
        $cached = $this->users[$userId] ?? null;
        if ($cached !== null) {
            return $cached;
        }
        $query = "SELECT * FROM users WHERE id = :user_id";
        $params = ['user_id' => $userId];
        $results = $this->db->fetchOne($query, $params);
        if (!$results) {
            return null;
        }
        $user = User::fromArray($this, $results);
        $this->addUser($user);
        return $user;
    }

    public function getGame(int $id): ?Game
    {
        $cached = $this->games[$id] ?? null;
        if ($cached !== null) {
            return $cached;
        }
        $query = "SELECT * FROM games WHERE id = :game_id";
        $params = ['game_id' => $id];
        $results = $this->db->fetchOne($query, $params);
        if (empty($results)) {
            return null;
        }

        $game = Game::fromArray($this, $results);
        $this->addGame($game);
        return $game;
    }

    public function getCollection(int $collectionId): ?Collection
    {
        foreach ($this->collections as $userCollections) {
            foreach ($userCollections as $collection) {
                if ($collection->id === $collectionId) {
                    return $collection;
                }
            }
        }
        $query = "SELECT * FROM collections WHERE id = :collection_id";
        $params = ['collection_id' => $collectionId];
        $results = $this->db->fetchOne($query, $params);
        if (empty($results)) {
            return null;
        }
        $collection = Collection::fromArray($this, $results);
        $this->addCollection($collection->user_id, $collection);
        return $collection;
    }

    public function getUserCollections(int $userId): array
    {
        $cached = $this->collections[$userId] ?? null;
        if ($cached !== null) {
            return $cached;
        }
        $query = "SELECT * FROM collections WHERE user_id = :user_id";
        $params = ['user_id' => $userId];
        $results = $this->db->fetchAll($query, $params);
        if (!$results) {
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



function getRuntime(): Runtime
{
    return $_SESSION['runtime'] ?? new Runtime();
}
