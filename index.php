<?php
require_once "vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use TeamCherry\MusicMuse\App;
use TeamCherry\MusicMuse\Album;

// Create app from App class
$app = new App();
$site_name = $app -> site_name;
$albumModel = new Album();
// $site_name = "Music Muse";
$page_title = "$site_name | Home";

// Gets all albums from the database
$albums = $albumModel->getAllAlbums();

// Generate the HTML for the album cards
$albumCardsHtml = '';
foreach ($albums as $album) {
    $albumCardsHtml .= Album::getAlbumCard($album); // Use the static method
}

// Loading the twig template
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment( $loader );
$template = $twig -> load( 'home.twig' );

// // Render the ouput
echo $template -> render( [ 
    'title' => $page_title, 
    'website_name' => $site_name,
    'album_cards' => $albumCardsHtml,
] );
?>