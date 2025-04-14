<?php

namespace TeamCherry\MusicMuse;

use \Exception;
use TeamCherry\MusicMuse\Database;

class Song extends Database
{
    public function __construct()
    {
        parent::__construct();
    }

    // Returns an array of the songs in an album ordered by the song_id
    public function getSongsByAlbum(int $albumId): ?array
    {
        $query = "
            SELECT
                song_id,
                song_title,
                duration
            FROM
                songs
            WHERE
                album_id = :album_id
            ORDER BY
                song_id ASC
        ";


        try {
            $statement = $this->connection->prepare($query);
            $statement->bind_param("i", $albumId);
            $statement->execute();
            $result = $statement->get_result();

            if ($result && $result->num_rows > 0) {
                $songs = [];
                while ($row = $result->fetch_assoc()) {
                    $songs[] = $row;
                }
                return $songs;
            } else {
                return null; // Return null if no songs found
            }
        } catch (Exception $e) {
            error_log("Database error in Song->getSongsByAlbum: " . $e->getMessage());
            throw $e;
        }
    }
}
