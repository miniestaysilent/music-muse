<?php
require_once "vendor/autoload.php";

use TeamCherry\MusicMuse\App;

$app = new App();

// Create data variables
$site_name = "Music Muse";

// Loading the twig template
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment( $loader );
$template = $twig -> load( 'login.twig' );

// // Render the ouput
echo $template -> render( [ 
        'website_name' => $site_name,
    ] );
?>