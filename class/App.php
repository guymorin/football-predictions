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
    
    public static function checkInstall(){
        $val = false;
        $dir = 'install';
        $files = scandir($dir);
        foreach($files as $f){
            if($f == 'AppConnect.inc') $val = true;
        }
        return $val;
    }
    
    public static function getDb(){
        if(self::$database == null){
            $info = self::checkInstall();
            if($info){
                require 'install/AppConnect.inc';
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

    public static function setTitle($pdo){
        $counter = 0;
        if(self::checkInstall()){
            $req = "SELECT name FROM fp_preferences LIMIT 0,1;";
            $data = $pdo->prepare($req,null,true);
            $counter = $pdo->rowCount();
        }
        if($counter>0){
            foreach ($data as $d) {
                self::$title = $d->name;
            }
        } else {
            self::$title = Language::title('site');
        }
    }
    
    public static function exitNoAdmin(){
        if($_SESSION['role']!='2') header("Location:index.php");
    }
}
?>
