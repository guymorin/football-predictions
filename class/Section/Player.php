<?php 
/**
 * 
 * Class Player
 * Manage Player page
 */
namespace FootballPredictions\Section;
use \PDO;

class Player
{
    public function __construct(){

    }
    
    static function submenu($db){
        require '../lang/fr.php';
        $response = $db->query("SELECT * FROM player ORDER BY name, firstname");
        echo "  	<a href='/'>$title_homepage</a>";
        echo "<a href='index.php?page=player'>$title_bestPlayers</a>";
        echo "<a href='index.php?page=player&create=1'>$title_createAPlayer</a>";
        echo "<a href='index.php?page=player&modify=1'>$title_modifyAPlayer</a>\n";
        $response->closeCursor();
    }
}
?>