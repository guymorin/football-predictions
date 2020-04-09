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
    
    static function submenu($pdo, $form){
        require '../lang/fr.php';
        $val = "  	<a href='/'>$title_homepage</a>";
        $val .= "<a href='index.php?page=marketValue'>$title_marketValue</a>";
        $val .= "<a href='index.php?page=team&create=1'>$title_createATeam</a>\n";
        $req = "SELECT c.* FROM team c 
        LEFT JOIN season_championship_team scc ON c.id_team=scc.id_team 
        WHERE scc.id_season = " . $_SESSION['seasonId'] . "
        AND scc.id_championship = " . $_SESSION['championshipId'] . " 
        ORDER BY name;";
        $data = $pdo->query($req);
        $counter = $pdo->rowCount();
        if($counter > 1){
            $val .= "<form action='index.php?page=team' method='POST'>\n";
            $val .= $form->inputAction('modify');
            $val .= $form->label($title_modifyATeam);
            $val .= $form->selectSubmit('id_team', $data);
            $val .= "</form>\n";
        }
        return $val;
    }
    
    static function createForm($pdo, $error, $form, $teamName, $weatherCode){
        require '../lang/fr.php';
        $val = $error->getError();
        $val .= "<form action='index.php?page=team' method='POST'>\n";
        $val .= $form->inputAction('create');
        $form->setValue('name', $teamName);
        $form->setValue('weather_code', $weatherCode);
        $val .= $form->input($title_name, 'name');
        $val .= $form->input($title_weathercode, 'weather_code');
        $val .= $form->submit($title_create);
        $val .= "</form>\n";  
        return $val;
    }
    
    static function modifyForm($pdo, $data, $error, $form, $teamId){
        require '../lang/fr.php';
        $val .= $error->getError();
        $val .= "<form action='index.php?page=team' method='POST'>\n";
        $form->setValues($data);
        $val .= $form->inputAction('modify');
        $val .= $form->input($title_name, 'name');
        $val .= $form->input($title_weathercode, 'weather_code');
        $val .= $form->submit($title_modify);
        $val .= "</form>\n";
        // Delete
        $val .= "<form action='index.php?page=team' method='POST' onsubmit='return confirm()'>\n";
        $val .= $error->getError();
        $val .= $form->inputAction('delete');
        $val .= $form->inputHidden('id_team',$teamId);
        $val .= $form->inputHidden('name',$data->name);
        $val .= $form->submit("&#9888 $title_delete &#9888");
        $val .= "</form>\n";
        return $val;
    }
}
?>