<?php
namespace TeamCherry\MusicMuse;

use \Exception;
use TeamCherry\MusicMuse\Database;

class Artist extends Database
{
    public function __construct()
    {
        parent::__construct();
    }

    // Gets artist details from an album ID
    public function getArtistByAlbumId(int $albumId): ?array
    {
        $query = "
            SELECT
                Artist.artist_id,
                Artist.artist_name,
                Artist.artist_image
            FROM
                Album
            INNER JOIN Artist ON Album.artist_id = Artist.artist_id
            WHERE
                Album.album_id = ?
            LIMIT 1
        ";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bind_param("i", $albumId);
            $statement->execute();
            $result = $statement->get_result();

            if ($result && $result->num_rows > 0) {
                return $result->fetch_assoc();
            } else {
                return null;
            }
        } catch (Exception $e) {
            error_log("Database error in Artist->getArtistByAlbumId: " . $e->getMessage());
            throw $e;
        }
    }


    public function searchArtists(string $searchTerm): array
    {
        $query = "
            SELECT
                artist_id,
                artist_name,
                artist_image
            FROM
                Artist
            WHERE
                artist_name LIKE ?
        ";
        // '%' to match anywhere in the artist name
        $searchTerm = "%" . $searchTerm . "%";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bind_param("s", $searchTerm);
            $statement->execute();
            $result = $statement->get_result();

            if ($result && $result->num_rows > 0) {
                $artists = [];
                while ($row = $result->fetch_assoc()) {
                    $artists[] = $row;
                }
                return $artists;
            } else {
                return []; // Return an empty array if no artists are found
            }
        } catch (Exception $e) {
            error_log("Database error in Artist->searchArtists: " . $e->getMessage());
            throw $e;
        }
    }
}

