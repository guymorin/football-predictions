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
    const DB_NAME="fp";
    const DB_USER="fp";
    const DB_PASS="fp";
    
    private static $title;
    private static $database;
    
    public function __construct(){
        self::$database = null;
    }
    
    public static function getDb(){
        if(self::$database == null){
            self::$database = new Database(self::DB_HOST, self::DB_NAME, self::DB_USER, self::DB_PASS);
        }
        return self::$database;
    }
    
    public static function getTitle(){
        return self::$title;
    }

    public static function setTitle($title){
        self::$title = $title;
    }
    
    public static function exitNoAdmin(){
        if($_SESSION['role']!='2') header("Location:index.php");
    }
}
?>
