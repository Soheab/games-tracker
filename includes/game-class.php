<?php
include_once 'db.php';
include_once '../run.php';

include_once 'base.php';

class Game extends DatabaseHandler {
    protected string $tableName = 'games';

    public string $title;
    public string $genre;
    public string $platform;
    public int $release_year;
    public string $added_at;


    public function __construct(
        Runtime $runtime,
        ?int $id, string $title, string $genre, string $platform, int $release_year, string $added_at
    ) {
        parent::__construct($runtime , $id);
        $this->title = $title;
        $this->genre = $genre;
        $this->platform = $platform;
        $this->release_year = $release_year;
        $this->added_at = $added_at;
    }

    public function delete(): bool {
        if ($this->id === null) {
            return false;
        }
        $query = "DELETE FROM games WHERE id = :id";
        $params = ['id' => $this->id];
        $this->__runtime->db->execute($query, $params);
        return true;
    }

}