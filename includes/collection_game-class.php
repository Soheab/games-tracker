<?php
include_once __DIR__ . '/../run.php';

include_once 'base.php';
include_once 'game-class.php';
include_once 'collection-class.php';

enum Status: string {
    case WISHLIST = 'wishlist';
    case PLAYING = 'playing';
    case COMPLETED = 'completed';
    case DROPPED = 'dropped';
}


class CollectionGame extends DatabaseHandler {
    protected static string $tableName = 'collection_game';

    public int $collection_id;
    public int $game_id;
    public Status $status;
    public string $added_at;

    public function __construct(
        Runtime $runtime,
        ?int $id,
        int $collection_id,
        int $game_id,
        string $status,
        string $added_at
    ) {
        parent::__construct($runtime, $id);
        $this->collection_id = $collection_id;
        $this->game_id = $game_id;
        $this->status = Status::from($status);
        $this->added_at = $added_at;
    }

    public function getCollection(): Collection {
        return $this->__runtime->getCollection($this->collection_id);
    }

    public function getGame(): Game {
        return $this->__runtime->getGame($this->game_id);
    }

}