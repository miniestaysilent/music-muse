 <?php

    require_once "vendor/autoload.php";



    use TeamCherry\MusicMuse\App;

    use TeamCherry\MusicMuse\Album;

    use TeamCherry\MusicMuse\Song;



    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);

    $dotenv->load();



    // Create app from App class

    $app = new App();

    $site_name = $app->site_name;



    $page_title = "$site_name | Home";



    // Album model

    $albumModel = new Album();

    $songModel = new Song();



    // Get all album cards

    $albums = $albumModel->getAllAlbums();

    $albumCardsHtml = '';

    foreach ($albums as $album) {

        $albumCardsHtml .= Album::getAlbumCard($album);
    }



    // Get recommended album ID

    $recommendedAlbumId = Album::getRandomAlbumId();



    $recommendedAlbum = null;

    $recommendedTracks = [];



    if ($recommendedAlbumId !== null) {
        $recommendedAlbum = $albumModel->getDetail($recommendedAlbumId);

        $recommendedTracks = $songModel->getSongsByAlbum($recommendedAlbumId);
    }



    // Load Twig template

    $loader = new \Twig\Loader\FilesystemLoader('templates');

    $twig = new \Twig\Environment($loader);

    $template = $twig->load('home.twig');



    // Render page

    echo $template->render([

        'title' => $page_title,

        'website_name' => $site_name,

        'album_cards' => $albumCardsHtml,

        'recommended_album' => $recommendedAlbum,

        'tracks' => $recommendedTracks,

    ]);
