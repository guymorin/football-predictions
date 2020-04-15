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
        require "../lang/fr.php";
        return $array[$val];
    }
}
?>