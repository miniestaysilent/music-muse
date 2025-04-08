<?php
require_once "vendor/autoload.php";

// Classes used on this page
use TeamCherry\MusicMuse\App;

// Create app from App class
$app = new App();
$site_name = $app -> site_name;

// Create data variables
$page_title = $site_name . "|". "Login";

// Loading the twig template
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment( $loader );
$template = $twig -> load( 'login.twig' );

// Render the ouput
echo $template -> render( [ 
    'title' => $page_title,
    'website_name' => $site_name
] );
?>