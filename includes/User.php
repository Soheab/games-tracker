<?php

namespace App;

class User extends DatabaseHandler
{
    protected static string $tableName = 'users';

    public string $username;
    public string $email;
    public string $password;
    public string $created_at;
    public Role $role;

    public function __construct(
        Runtime $runtime,
        ?int $id,
        string $username,
        string $email,
        string $password,
        Role $role = Role::USER
    ) {
        parent::__construct($runtime, $id);
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->created_at = date('Y-m-d H:i:s');
        $this->role = $role;
    }

    public static function userIsUnique(Runtime $runtime, string $username, string $email): bool
    {
        $query = "SELECT COUNT(*) as count FROM ".static::$tableName." WHERE username = :username OR email = :email";
        $params = ['username' => $username, 'email' => $email];
        $result = $runtime->db->fetchOne($query, $params);

        return $result['count'] === 0;
    }

    public static function handleArray(array $data): array
    {
        if (isset($data['role']) && is_string($data['role'])) {
            $data['role'] = Role::from($data['role']);
        }

        return $data;
    }

    public static function fromArray(Runtime $runtime, array $data): static
    {
        return new static(
            $runtime,
            $data['id'] ?? null,
            $data['username'],
            $data['email'],
            $data['password'],
            Role::from($data['role']) ?? Role::USER,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'created_at' => $this->created_at,
            'role' => $this->role->value,
        ];
    }

    public function getCollections(): array
    {
        return $this->runtime->getUserCollections($this->id);
    }

    public function getCollection(
        int $collectionId
    ): ?Collection {
        $userCollections = $this->runtime->getUserCollections($this->id);

        foreach ($userCollections as $collection) {
            if (($collection->id ?? null) === $collectionId) {
                return $collection;
            }
        }

        return null;
    }
}
