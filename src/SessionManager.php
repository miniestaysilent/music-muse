<?php
namespace TeamCherry\MusicMuse;

// Creates a cookie and tracks the user's session

class SessionManager {
    public static function init() {
        if( session_status() == PHP_SESSION_NONE ) {
            session_start();
        }
    }
    public static function kill() {
        session_destroy();
    }
}
?>