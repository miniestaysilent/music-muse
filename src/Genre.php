<?php

namespace TeamCherry\MusicMuse;

use \Exception;
use TeamCherry\MusicMuse\Database;

class Genre extends Database {

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllGenres(): array
    {
        $query = "SELECT genre_id, genre_name FROM Genre ORDER BY genre_name ASC";
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $result = $statement->get_result();
        $genres = [];
        while ($row = $result->fetch_assoc()) {
            $genres[] = $row;
        }
        return $genres;
    }

    public function getAlbumsByGenre(int $genre_id): array
    {
        $query = "
            SELECT
                Album.album_id AS album_id,
                Album.album_title AS album_title,
                Album.artist_id AS artist_id,
                Artist.artist_name AS artist_name,
                Album.release_date AS release_date,
                Album.cover_image AS cover_image
            FROM
                `Album`
            INNER JOIN Artist ON Album.artist_id = Artist.artist_id
            INNER JOIN Album_Genre ON Album.album_id = Album_Genre.album_id
            WHERE Album_Genre.genre_id = ? AND Album.visible = 1
            ORDER BY Album.album_title ASC
        ";
        $statement = $this->connection->prepare($query);
        $statement->bind_param("i", $genre_id);
        $statement->execute();
        $albums = [];
        $result = $statement->get_result();
        while ($row = $result->fetch_assoc()) {
            $albums[] = $row;
        }
        return $albums;
    }
}
