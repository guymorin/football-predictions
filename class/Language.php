<?php
/**
 * 
 * Class Language
 * Generate a message in a selected language
 */
namespace FootballPredictions;

class Language
{
    static function title($val){
        require "../lang/{$_SESSION['language']}.php";
        return $array[$val];
    }
    
    static function getBrowserLang(){
        $val = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        switch($val){
            case 'en':
            case 'fr':
                $_SESSION['language'] = $val;
                break;
            default:
                $_SESSION['language'] = 'fr';
                break;
        }
    }
}
?>