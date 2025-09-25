<?php

namespace App;

class Game extends DatabaseHandler
{
    protected static string $tableName = 'games';

    public string $title;
    public string $genre;
    public string $platform;
    public int $release_year;
    public string $added_at;


    public function __construct(
        Runtime $runtime,
        ?int $id,
        string $title,
        string $genre,
        string $platform,
        int $release_year,
        string $added_at
    ) {
        parent::__construct($runtime, $id);
        $this->title = $title;
        $this->genre = $genre;
        $this->platform = $platform;
        $this->release_year = $release_year;
        $this->added_at = $added_at;
    }

    public static function fromArray(Runtime $runtime, array $data): static
    {
		return new static(
			$runtime,
            $data['id'] ?? null,
            $data['title'],
            $data['genre'],
            $data['platform'],
            $data['release_year'],
            $data['added_at']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'genre' => $this->genre,
            'platform' => $this->platform,
            'release_year' => $this->release_year,
            'added_at' => $this->added_at,
        ];
    }
}
