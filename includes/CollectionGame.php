<?php

namespace App;

class CollectionGame extends DatabaseHandler
{
    protected static string $tableName = 'collection_game';
    public string $added_at;
    public int $collection_id;
    public int $game_id;
    public Status $status;

    /**
     * @param Runtime $runtime
     * @param int|null $id
     * @param int $collection_id
     * @param int $game_id
     * @param string $status
     * @param string $added_at
     */
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

    public static function fromArray(Runtime $runtime, array $data): static
    {
		return new static(
			$runtime,
            $data['id'] ?? null,
            $data['collection_id'],
            $data['game_id'],
            $data['status'],
            $data['added_at']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'collection_id' => $this->collection_id,
            'game_id' => $this->game_id,
            'status' => $this->status->value,
            'added_at' => $this->added_at,
        ];
    }

    public function getCollection(): Collection
    {
        return $this->runtime->getCollection($this->collection_id);
    }

    /**
     * @return Game
     */
    public function getGame(): Game
    {
        return $this->runtime->getGame($this->game_id);
    }
}
