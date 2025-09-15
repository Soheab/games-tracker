<?php
include_once 'db.php';
include_once '../run.php';

include_once 'base.php';


enum Role: int {
    case USER = 0;
    case ADMIN = 1;
}

class User extends DatabaseHandler {
    protected string $tableName = 'collections';

    public string $username;
    public string $email;
    public string $password;
    public string $created_at;
    public Role $role;

    public function __construct(
        Runtime $runtime,
        ?int $id, string $username, string $email, string $password, string $created_at, Role $role = Role::USER
    ) {
        parent::__construct($runtime, $id);
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->created_at = $created_at;
        $this->role = $role;
    }

    public function getCollections(): array {
        return $this->__runtime->getUserCollections($this->id);
    }

    public function getCollection(int $collectionId): ?Collection {
        return $this->__runtime->getCollection($this->id, $collectionId);
    }
}