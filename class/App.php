<?php 
/**
 *
 * Class App
 * Initialize App
 *
 */
namespace FootballPredictions;

class App
{

    const DB_HOST="localhost";
    const DB_NAME="phpmyadmin";
    const DB_USER="phpmyadmin";
    const DB_PASS="master";
    
    private static $title;
    private static $database;
    
    public static function getDb(){
        if(self::$database == null){
            self::$database = new Database(self::DB_HOST, self::DB_NAME, self::DB_USER, self::DB_PASS);
        }
    }
    
    public static function getTitle(){
        return self::$title;
    }

    public static function setTitle($title){
        self::$title = $title . (isset($_SESSION['seasonName']) ? " " . $_SESSION['seasonName'] : null);
    }
}
?>