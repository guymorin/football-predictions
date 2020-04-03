<?php 
/**
 * 
 * Class Team
 * Manage Team page
 */
namespace FootballPredictions\Section;
use \PDO;

class Team
{
    public function __construct(){

    }
    
    static function submenu($db){
        require '../lang/fr.php';
        echo "  	<a href='/'>$title_homepage</a>";
        echo "<a href='index.php?page=marketValue'>$title_marketValue</a>";
        echo "<a href='index.php?page=team&create=1'>$title_createATeam</a>\n";
        echo "  	<form action='index.php?page=team' method='POST'>\n";
        echo "          <input type='hidden' name='modify' value='1'>\n";
        echo "          <label>$title_modifyATeam :</label>\n";
        echo "  	   <select name='id_team' onchange='submit()'>\n";
        $response = $db->query("SELECT c.* FROM team c LEFT JOIN season_championship_team scc ON c.id_team=scc.id_team WHERE scc.id_season='".$_SESSION['seasonId']."' AND scc.id_championship='".$_SESSION['championshipId']."' ORDER BY name;");
        require '../pages/team_select.php';
        $response->closeCursor();
        echo "	       </select>\n";
        echo "         <noscript><input type='submit'></noscript>\n";
        echo "	    </form>\n";
    }
}
?>