<?php
include_once __DIR__ . '/../run.php';

include_once 'base.php';
include_once 'collection_game-class.php';


class Collection extends DatabaseHandler {
    protected static string $tableName = 'collections';

    public string $name;
    public string $description;
    public string $image_file;
    public int $user_id;
    public string $created_at;
    public ?string $updated_at = null;

    /** @var array<int, CollectionGame> */
    public array $games = [];

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
    }

    /**
     * Retrieves an array of CollectionGame objects
     * 
     * @return array<int, CollectionGame> 
     */
    // 
    public function getGames(): array {
        if (!empty($this->games)) {
            return $this->games;
        }
        $query = "SELECT * FROM collection_game WHERE collection_id = :collection_id";
        $params = ['collection_id' => $this->id];
        $results = $this->__runtime->db->execute($query, $params);
        foreach ($results as $row) {
            $cgame = CollectionGame::fromArray($this->__runtime, $row);
            $this->games[$cgame->id] = $cgame;
        }
        return $this->games;
    }
}