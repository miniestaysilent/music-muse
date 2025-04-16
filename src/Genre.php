<?php
namespace TeamCherry\MusicMuse;

use \Exception;
use TeamCherry\MusicMuse\Database;

class Genre extends Database
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getGenresByAlbumId(int $albumId): ?array
    {
        $query = "
            SELECT
                Genre.genre_id,
                Genre.name
            FROM
                Album_Genre
            INNER JOIN Genre ON Album_Genre.genre_id = Genre.genre_id
            WHERE
                Album_Genre.album_id = ?
        ";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bind_param("i", $albumId);
            $statement->execute();
            $result = $statement->get_result();

            if ($result && $result->num_rows > 0) {
                $genres = [];
                while ($row = $result->fetch_assoc()) {
                    $genres[] = $row;
                }
                return $genres;
            } else {
                return null;
            }
        } catch (Exception $e) {
            error_log("Database error in Genre->getGenresByAlbumId: " . $e->getMessage());
            throw $e;
        }
    }
}
