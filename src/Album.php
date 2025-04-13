<?php

namespace TeamCherry\MusicMuse;

use Exception;
use TeamCherry\MusicMuse\Database;

class Album extends Database
{
    public function __construct()
    {
        parent::__construct();
    }


    private static function getArtistName(int $album_id): ?string
    {
        $instance = new self();
        $query = "
            SELECT Artist.artist_name
            FROM Album
            INNER JOIN Artist ON Album.artist_id = Artist.artist_id
            WHERE Album.album_id = ?
        ";
        $statement = $instance->connection->prepare($query);
        $statement->bind_param("i", $album_id);
        $statement->execute();
        $result = $statement->get_result();
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['artist_name'];
        }
        return "Unknown Artist"; // Default if not found
    }

    private static function getReleaseYear(int $album_id): ?string
    {
        $instance = new self();
        $query = "SELECT release_date FROM Album WHERE album_id = ?";
        $statement = $instance->connection->prepare($query);
        $statement->bind_param("i", $album_id);
        $statement->execute();
        $result = $statement->get_result();
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['release_date']) {
                return date('Y', strtotime($row['release_date']));
            }
        }
        return null;
    }

    private static function getCoverImage(int $album_id): ?string
    {
        $instance = new self();
        $query = "SELECT cover_image FROM Album WHERE album_id = ?";
        $statement = $instance->connection->prepare($query);
        $statement->bind_param("i", $album_id);
        $statement->execute();
        $result = $statement->get_result();
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return 'assets/album_covers/' . $row['cover_image'];
        }
        return 'assets/album_covers/default.jpg'; // Default 
    }

    private static function getTrackList(int $album_id): array
    {
        $instance = new self();
        $track_query = "
            SELECT
                track_name AS name
            FROM
                `tracks`
            WHERE
                album_id = ?
            ORDER BY
                track_number ASC
        ";
        $statement = $instance->connection->prepare($track_query);
        $statement->bind_param("i", $album_id);
        $statement->execute();
        $result = $statement->get_result();
        $tracks = [];
        while ($row = $result->fetch_assoc()) {
            $tracks[] = $row;
        }
        return $tracks;
    }

    // Gets the Average rating 
    public function getAvgRating(int $album_id): ?int
    {
        $rating_query = "
            SELECT AVG(star_rating) AS average_rating
            FROM reviews
            WHERE album_id = ?
        ";
        $statement = $this->connection->prepare($rating_query);
        $statement->bind_param("i", $album_id);
        $statement->execute();
        $result = $statement->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['average_rating'] !== null) {
                return (int) round($row['average_rating']);
            }
        }
        return 0;
    }

    // Display the star rating
    private static function renderStarRating(int $rating): string
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $stars .= '<span class="fa fa-star checked"></span>';
            } else {
                $stars .= '<span class="fa fa-star"></span>';
            }
        }
        return $stars;
    }

    public static function getArtist(int $album_id): ?string
    {
        $instance = new self(); //Access the connection
        $get_artist_query = "
            SELECT
                Artist.artist_name
            FROM
                `Album`
            INNER JOIN
                Artist ON Album.artist_id = Artist.artist_id
            WHERE
                Album.album_id = ?
        ";
        $statement = $instance->connection->prepare($get_artist_query);
        $statement->bind_param("i", $album_id);
        $statement->execute();
        $result = $statement->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['artist_name'];
        } else {
            return "Unknown Artist";
        }
    }

    // This returns an array of all the albums
    public function getAllAlbums(): array
    {
        $get_query = "
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
            WHERE Album.visible = 1
            ORDER BY Album.album_title ASC
        ";
        $statement = $this->connection->prepare($get_query);
        $statement->execute();
        $albums = [];
        $result = $statement->get_result();
        while ($row = $result->fetch_assoc()) {
            $albums[] = $row;
        }
        return $albums;
    }


    // Add function to get an array of the genres

    // Creates a clickable album card that navigates to the respective details page
    public static function getAlbumCard(array $albumData): string
    {
        $albumId = $albumData['album_id'] ?? null; // Assuming your $albumData has 'album_id'
        $coverImage = isset($albumData['cover_image']) ? htmlspecialchars($albumData['cover_image']) : 'assets/images/default_cover.png';
        $artistName = isset($albumData['artist_name']) ? htmlspecialchars($albumData['artist_name']) : 'Unknown Artist';
        $releaseYear = isset($albumData['release_date']) ? htmlspecialchars(date('Y', strtotime($albumData['release_date']))) : 'Unknown Year';
        $starRating = self::renderStarRating(0);
    
        $html = '<div class="album-card">';
        if ($albumId) {
            $html .= '<a href="/album/' . htmlspecialchars($albumId) . '">'; // Link to the details page
            $html .= '<img src="' . $coverImage . '" alt="' . htmlspecialchars($albumData['album_title'] ?? 'Album Cover') . '" class="album-cover">';
            $html .= '</a>';
        } else {
            $html .= '<img src="' . $coverImage . '" alt="' . htmlspecialchars($albumData['album_title'] ?? 'Album Cover') . '" class="album-cover">';
        }
        $html .= '<div class="album-details">';
        $html .= '<div class="star-rating">' . $starRating . '</div>';
        $html .= '<p class="artist-name">' . $artistName . '</p>';
        $html .= '<p class="release-year">' . $releaseYear . '</p>';
        $html .= '</div>';
        $html .= '</div>';
    
        return $html;
    }

    //In this version this function selects a random album
    private static function getRecommendedAlbum(): ?array
    {
        $instance = new self();
        $count_query = "SELECT COUNT(*) AS total FROM Album WHERE visible = 1";
        $count_statement = $instance->connection->prepare($count_query);
        $count_statement->execute();
        $count_result = $count_statement->get_result();
        $total_albums = $count_result->fetch_assoc()['total'];

        if ($total_albums > 0) {
            $random_offset = rand(0, $total_albums - 1);
            $random_album_query = "
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
            WHERE Album.visible = 1
            LIMIT 1 OFFSET ?
        ";
            $statement = $instance->connection->prepare($random_album_query);
            $statement->bind_param("i", $random_offset);
            $statement->execute();
            $result = $statement->get_result();
            if ($result && $result->num_rows > 0) {
                return $result->fetch_assoc(); // Returns an array with all necessary album details
            }
        }
        return null;
    }
}
