<?php 
/**
 * 
 * Class Matchday
 * Manage Matchday page
 */
namespace FootballPredictions\Section;
use \PDO;

class Matchday
{
    public function __construct(){

    }
    
    static function submenu($db){
        require '../lang/fr.php';
        echo "  	<a href='/'>$title_homepage</a>";
        if(isset($_SESSION['matchdayId'])){
            echo "<a href='index.php?page=matchday'>$title_statistics</a>";
            echo "<a href='index.php?page=prediction'>$title_predictions</a>";
            echo "<a href='index.php?page=results'>$title_results</a>";
            echo "<a href='index.php?page=teamOfTheWeek'>$title_teamOfTheWeek</a>";
            echo "<a href='index.php?page=match&create=1'>$title_createAMatch</a>";
            echo "<a href='index.php?page=match&modify=1'>$title_modifyAMatch</a>";
        } else echo "<a href='index.php?page=matchday&create=1'>$title_createAMatchday</a>\n";
    }
}
?>