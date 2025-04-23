<?php
require_once 'vendor/autoload.php';

use TeamCherry\MusicMuse\App;
use TeamCherry\MusicMuse\SessionManager;

$app = new App();

// Ends current session
SessionManager::kill();

header("location: /");
?>