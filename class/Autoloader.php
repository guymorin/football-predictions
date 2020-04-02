<?php
/**
 * 
 * Class Autoloader
 * Load all classes
 *
 */
class Autoloader
{
    static function register(){
        spl_autoload_register(array(__CLASS__,"autoload"));
    }
    static function autoload(){
        require("class/".$class_name.".php");
    }
}
?>