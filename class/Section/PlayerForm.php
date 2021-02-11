<?php 
/**
 * 
 * Class Player Form
 * Manage forms in player page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Theme;
use \PDO;

class PlayerForm
{
    public function __construct(){

    }
    
    static function createForm($pdo, $error, $form){
        $val = '';
        $val .= "<form action='index.php?page=player' method='POST'>\n";
        $val .= $form->inputAction('create');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('player')) . "</legend>\n";
        $val .= $error->getError();
        $val .= $form->input(Language::title('name'), 'name', 'name');
        $val .= "<!-- Response --><div id='uname_response'></div>";// Warning
        $val .= "<br />\n";
        $val .= $form->input(Language::title('firstname'), 'firstname');
        $val .= "<br />\n";
        $val .= $form->inputRadioPosition();
        $val .= $form->selectTeam($pdo,'id_team',null,null,true);
        $val .= "</fieldset>\n";
        $val .= "<fieldset>\n";
        $val .= $form->submit(Theme::icon('create')." "
                .Language::title('create'));
        $val .= "</fieldset>\n";
        $val .= "</form>\n";
        return $val;
    }
    
    static function modifyForm($pdo, $error, $form, $playerId){
        
        // Table of players
        $req ="SELECT s.name as season, c.name as team
        FROM player j
        LEFT JOIN season_team_player scj ON j.id_player=scj.id_player
        LEFT JOIN team c ON scj.id_team=c.id_team
        LEFT JOIN season s ON s.id_season=scj.id_season
        WHERE j.id_player=:id_player
        ORDER BY season DESC;";
        $data = $pdo->prepare($req,[
            'id_player' => $playerId
            
        ],true);
        $counter = $pdo->rowCount();
        $isThisSeason = false;
        if($counter>0){
            $table = "<table>\n";
            $table .= "   <tr>\n";
            $table .= "       <th>" . (Language::title('season')) . "</th>\n";
            $table .= "       <th>" . (Language::title('team')) . "</th>\n";
            $table .= "   </tr>\n";
            foreach($data as $d){
                $table .= "   <tr>\n";
                $table .= "       <td>" . $d->season . "</td>\n";
                if($d->season==$_SESSION['seasonName']) $isThisSeason = true;
                $table .= "       <td>" . $d->team . "</td>\n";
                $table .= "   </tr>\n";
            }
            $table .= "</table>\n";
        }
        
        $req ="SELECT j.id_player, j.name, j.firstname, j.position, c.id_team
        FROM player j
        LEFT JOIN season_team_player scj ON j.id_player=scj.id_player
        LEFT JOIN team c ON scj.id_team=c.id_team
        WHERE j.id_player=:id_player  
        ORDER BY scj.id_season DESC;";
        $data = $pdo->prepare($req,[
            'id_player' => $playerId
        ]);
        $val = '';
        $val .= "<form action='index.php?page=player' method='POST'>\n";
        $form->setValues($data);
        $val .= $form->inputAction('modify');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('player')) . "</legend>\n";
        $val .= $error->getError();
        $val .= $form->inputHidden('id_player', $data->id_player);
        $val .= $form->input(Language::title('name'),'name');
        $val .= "<span id='msgbox' style='display:none'></span>";
        $val .= "<br />\n";
        $val .= $form->input(Language::title('firstname'),'firstname');
        $val .= "<br />\n";
        $val .= $form->inputRadioPosition($data);
        $val .= $form->selectTeam($pdo, null, $data->id_team, null, true);
        $val .= $table;
        $val .= "</fieldset>\n";
        $val .= "<fieldset>\n";
        $val .= $form->submit(Theme::icon('modify')." "
            .Language::title('modify'));
        if(!$isThisSeason){
            $val .= $form->submit(Theme::icon('add')." "
                .Language::title('addPlayerTo')." :<br />"
                .$_SESSION['championshipName']." "
                .$_SESSION['seasonName']);
        }
        $val .= "</form>\n";
        $val .= $form->deleteForm('player', 'id_player', $playerId, false, 'id_team', $data->id_team);
        $val .= "</fieldset>\n";
        
        return $val;
    }
}
?>