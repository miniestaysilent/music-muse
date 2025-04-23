<?php
require_once "vendor/autoload.php";

// Classes used on this page
use TeamCherry\MusicMuse\Album;
use TeamCherry\MusicMuse\App;
use TeamCherry\MusicMuse\Song;
use TeamCherry\MusicMuse\Artist;
use TeamCherry\MusicMuse\Genre;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Create app from App class
$app = new App();
$site_name = $app->site_name;

$album = new Album();
$song = new Song();

$albumDetails = array();
$tracks = array();

if (isset($_GET['id'])) {
    $albumId = filter_var($_GET['id'], FILTER_VALIDATE_INT); // Sanitize input

    if ($albumId !== false) {
        $albumDetails = $album->getDetail($albumId);

        if (!$albumDetails) {
            http_response_code(404);
            echo "Album not found";
            exit;
        }

        $tracks = $song->getSongsByAlbum($albumId); // ✅ Fetch tracklist
    } else {
        http_response_code(400); // Bad Request
        echo "Invalid Album ID";
        exit;
    }
} else {
    http_response_code(400); // Bad Request
    echo "Album ID is missing";
    exit;
}

//Load Artist
$artist = new Artist();
$artistDetails = $artist->getArtistByAlbumId($albumId);

// Load genre list
$genre = new Genre();
$genres = $genre->getGenresByAlbumId($albumId);

// Load Average rating for display
$rating = $album->getAvgRating($albumId); // Get the rating
$ratingHtml = Album::renderStarRating($rating ?? 0); // Render stars

$page_title = $site_name . " | " . $albumDetails['album_title'] . " Details";

// Load Twig template
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);
$template = $twig->load('details.twig');

// Render template
echo $template->render([
    'title'          => $page_title,
    'website_name'   => $site_name,
    'album'          => $albumDetails,
    'tracks'         => $tracks,
    'artist'         => $artistDetails, 
    'genres'         => $genres,
    'rating_html'    => $ratingHtml,
]);
?>
