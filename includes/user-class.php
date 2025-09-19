<?php
include_once __DIR__ . '/../run.php';

include_once 'base.php';


enum Role: int {
    case USER = 0;
    case ADMIN = 1;
}

class User extends DatabaseHandler {
    protected static string $tableName = 'users';

    public string $username;
    public string $email;
    public string $password;
    public string $created_at;
    public Role $role;

    public function __construct(
        Runtime $runtime,
        ?int $id, string $username, string $email, string $password, Role $role = Role::USER
    ) {
        parent::__construct($runtime, $id);
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->created_at = date('Y-m-d H:i:s');
        $this->role = $role;
    }

    public function getCollections(): array {
        return $this->__runtime->getUserCollections($this->id);
    }

    public function getCollection(int $collectionId): ?Collection {
        return $this->__runtime->getUserCollections($this->id)[$collectionId] ?? null;
    }
}