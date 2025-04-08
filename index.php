<?php
require_once "vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Classes used on this page
use TeamCherry\MusicMuse\App;
// // use TeamCherry\MusicMuse\Album;

// Create app from App class
$app = new App();
$site_name = $app -> site_name;

// Get items from database
// $book = new Book();
// $items = $book -> get();


// // Create data variables
// $site_name = "Music Muse";
$page_title = "$site_name | Home";
// // $greeting = "Welcome to $site_name";

// Loading the twig template
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment( $loader );
$template = $twig -> load( 'home.twig' );

// // Render the ouput
echo $template -> render( [ 
//     // 'title' => $page_title, 
//     // 'greeting' => $greeting,
    'website_name' => $site_name,
//     // 'items' => $items
] );
?>