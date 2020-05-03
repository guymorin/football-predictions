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
    private static $DB_HOST="";
    private static $DB_NAME="";
    private static $DB_USER="";
    private static $DB_PASS="";
    
    private static $title;
    private static $database;
    
    public function __construct(){
        self::$database = null;
    }
    
    public static function getDb(){
        if(self::$database == null){
            $info = false;
            $dir = 'class';
            $files = scandir($dir);
            foreach($files as $f){
                if($f == 'AppConnect.inc') $info = true;
            }
            if($info){
                require 'AppConnect.inc';
                self::$DB_HOST = $DB_HOST;
                self::$DB_NAME = $DB_NAME;
                self::$DB_USER = $DB_USER;
                self::$DB_PASS = $DB_PASS;
                self::$database = new Database(self::$DB_HOST, self::$DB_NAME, self::$DB_USER, self::$DB_PASS);
            }
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
