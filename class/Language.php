<?php
/**
 * 
 * Class Language
 * Generate a message in a selected language
 */
namespace FootballPredictions;

class Language
{
    static function title($val,$count=null){
        self::getBrowserLang();
        // Using default with gettext function and 'po' files 
        require_once "lang/default.php";
        $v = getMessage($val,$count);
        return $v;
    }
        
    static function getBrowserLang(){
        if(empty($_SESSION['language'])){
            $val = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            switch($val){
                case 'en':
                    $_SESSION['language'] = 'en_US';
                    break;
                case 'fr':
                    $_SESSION['language'] = 'fr_FR';
                    break;
                default:
                    $_SESSION['language'] = 'en_US';
                    break;
            }
        }
    }
}
?>