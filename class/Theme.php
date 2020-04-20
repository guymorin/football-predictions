<?php
/**
 * 
 * Class Theme
 * Generate theme elements
 */
namespace FootballPredictions;

use PDO;

class Theme
{
    
    static function getTheme(){
        if (empty($_SESSION['directory_name'])) $_SESSION['directory_name'] = 'default';
    }

    static function icon($val){
        self::getTheme();
        require "../public/theme/{$_SESSION['directory_name']}/icons.php";
        return $array[$val];
    }
    
    
    static function style(){
        self::getTheme();
        $url = "../public/theme/{$_SESSION['directory_name']}/style.css";
        $val = "<link rel='stylesheet' type='text/css' media='all' href='$url' />\n";
        return $val;
    }
}
?>