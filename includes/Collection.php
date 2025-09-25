<?php

namespace App;

class Collection extends DatabaseHandler
{
    protected static string $tableName = 'collections';
    public string $created_at;
    public string $description;
    /**
     * @var array<int, CollectionGame>
     */
    public array $games;
    public string $image_file;
    public string $name;
    public ?string $updated_at;
    public int $user_id;

    public function __construct(
        Runtime $runtime,
        ?int $id,
        int $user_id,
        string $name,
        string $description,
        string $image_file
    ) {
        parent::__construct($runtime, $id);
        $this->user_id = $user_id;
        $this->name = $name;
        $this->description = $description;
        $this->image_file = $image_file;
        $this->created_at = date('Y-m-d H:i:s');
        $this->games = [];
        $this->updated_at = null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'description' => $this->description,
            'image_file' => $this->image_file,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }


    public function getGames(): array
    {
        if (!empty($this->games)) {
            return $this->games;
        }

        $query =
            'SELECT * FROM collection_game WHERE collection_id = :collection_id';
        $params = ['collection_id' => $this->id];
        $results = $this->runtime->db->execute($query, $params);

        foreach ($results as $row) {
            $game = CollectionGame::fromArray($this->runtime, $row);
            $this->games[$game->id] = $game;
        }

        return $this->games;
    }


    public static function fromArray(Runtime $runtime, array $data): static
    {
        return new static(
            $runtime,
            $data['id'] ?? null,
            $data['user_id'],
            $data['name'],
            $data['description'],
            $data['image_file']
        );
    }
}
