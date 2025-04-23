<?php
require_once "vendor/autoload.php";

// Classes used on this page
use TeamCherry\MusicMuse\App;
use TeamCherry\MusicMuse\Album;
use TeamCherry\MusicMuse\Artist;

// Create app from App class
$app = new App();
$site_name = $app->site_name;

$search_term = $_GET['search'] ?? '';
$search_term = trim($search_term); 

$albumModel = new Album();
$artistModel = new Artist();

$albums = $albumModel->searchAlbums($search_term); // Search albums
$artists = $artistModel->searchArtists($search_term); // Search artists

// Combine the results
$results = [
    'albums' => $albums,
    'artists' => $artists,
];

$page_title = "$site_name | Search Results for \"$search_term\"";

// Loading the twig template
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);
$template = $twig->load('search_results.twig');

$albumCardsHtml = '';
// If no albums are found or the search term is empty
if (empty($search_term) || (empty($results['albums']) && empty($results['artists']))) {
    $albumCardsHtml = '<div class="no-results">Oops we couldn\'t find anything (._.)</div>';
} else {
    foreach ($results['albums'] as $album) {
        $albumCardsHtml .= Album::getAlbumCard($album);
    }
}

// Render the output
echo $template->render([
    'title' => $page_title,
    'website_name' => $site_name,
    'album_cards' => $albumCardsHtml,
    'search_term' => $search_term,
]);
?>
