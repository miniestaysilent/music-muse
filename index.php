<?php 

require_once "vendor/autoload.php";
// // // Classes used on this page
// // use Syeda\Classproject\App;
// // use Syeda\Classproject\Book;

// // Create app from App class
// // $app = new App();

// // Get items from database
// // $book = new Book();
// // $items = $book -> get();

// // $site_name = $app -> site_name;
// // // Create data variables
// $site_name = "Music Muse"
// // $page_title = "$site_name Music Muse";
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
;
?>