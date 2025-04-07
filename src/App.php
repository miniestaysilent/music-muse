<?php
namespace TeamCherry\MusicMuse;
# Namespace can be found in composer.json

use Dotenv\Dotenv;
use \Exception;

class App{
    protected $config;
    public $site_name;

    # Create Constructor
    public function __construct()
    {
        
    }

    private function loadConfig(){
        try{
            // cwd = current working directory
            $app_dir = getcwd();
            $dotenv = Dotenv::createImmutable($app_dir); 
            $dotenv->load();
            $this -> site_name = $_ENV['SITE_NAME'];
        } 
        catch( Exception $exception){
            $msg = $exception -> getMessage();
            exit($msg);
        }
    }
}
?>


