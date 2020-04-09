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
    
    static function submenu($pdo, $form){
        require '../lang/fr.php';
        $response = $pdo->query("SELECT * FROM player ORDER BY name, firstname");
        $val = "  	<a href='/'>$title_homepage</a>";
        $val .= "<a href='index.php?page=player'>$title_bestPlayers</a>";
        $val .= "<a href='index.php?page=player&create=1'>$title_createAPlayer</a>";
        $req = "SELECT id_player, name, firstname 
        FROM player
        ORDER BY name, firstname;";
        $data = $pdo->query($req);
        $counter = $pdo->rowCount();

        if($counter > 1){
            $val .= "<form action='index.php?page=player' method='POST'>\n";
            $val .= $form->inputAction('modify');
            $val .= $form->label($title_modifyAPlayer);
            $val .= $form->selectSubmit('id_player', $data);
            $val .= "</form>\n";
        }
        return $val;
    }
    
    static function createForm($pdo, $error, $form){
        require '../lang/fr.php';
        $val = $error->getError();
        $val .= "<form action='index.php?page=player' method='POST'>\n";
        $val .= $form->inputAction('create');
        
        $val .= $form->input($title_name, 'name') . $form->input($title_firstname, 'firstname');
        $val .= "<br />\n";
        
        $val .= $form->inputRadioPosition();
        $val .= "<br />\n";
        
        $val .= $form->selectTeam($pdo);
        $val .= "<br />\n";
        
        $val .= $form->submit($title_create);
        $val .= "</form>\n";
        return $val;
    }
    
    static function modifyForm($pdo, $data, $error, $form){
        require '../lang/fr.php';
        $val = $error->getError();
        
        $val .= "<form action='index.php?page=player' method='POST'>\n";
        $form->setValues($data);
        $val .= $form->inputAction('modify');
        $val .= $form->inputHidden('id_player', $data->id_player);
        $val .= $form->input($title_name,'name');
        $val .= $form->input($title_firstname,'firstname');
        $val .= "<br />\n";
        $val .= $form->inputRadioPosition($data);
        $val .= "<br />\n";
        $val .= $form->selectTeam($pdo, null, $data->id_team);
        $val .= "<br />\n";
        $val .= $form->submit($title_modify);
        $val .= "</form>\n";
        $val .= "<br />\n";
        $val .= "<form action='index.php?page=player' method='POST' onsubmit='return confirm()'>\n";
        $val .= $form->inputAction('delete');
        $val .= $form->inputHidden('id_team', $data->id_team);
        $val .= $form->inputHidden('id_player', $data->id_player);
        $val .= $form->inputHidden('name', $data->name);
        $val .= $form->inputHidden('firstname', $data->firstname);
        $val .= $form->submit("&#9888 $title_delete &#9888");
        $val .= "</form>\n";
        return $val;
    }
}
?>