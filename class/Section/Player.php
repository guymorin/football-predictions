<?php 
/**
 * 
 * Class Player
 * Manage Player page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Theme;
use \PDO;

class Player
{
    public function __construct(){

    }
    
    static function submenu($pdo, $form, $current = null){
        $val ='';
        $currentClass = " class='current'";
        $classBP = $classC = '';
        switch($current){
            case 'bestPlayers':
                $classBP = $currentClass;
                break;
            case 'create':
                $classC = $currentClass;
                break;
        }
        $response = $pdo->query("SELECT * FROM player ORDER BY name, firstname");
        $val = "  	<a href='/'>" . (Language::title('homepage')) . "</a>";
        $val .= "<a" . $classBP . " href='index.php?page=player'>" . (Language::title('bestPlayers')) . "</a>";
        $val .= "<a" . $classC . " href='index.php?page=player&create=1'>" . (Language::title('createAPlayer')) . "</a>";
        if(($_SESSION['role'])==2){
            $req = "SELECT id_player, name, firstname 
            FROM player
            ORDER BY name, firstname;";
            $data = $pdo->query($req);
            $counter = $pdo->rowCount();
    
            if($counter > 1){
                $val .= "<form action='index.php?page=player' method='POST'>\n";
                $val .= $form->inputAction('modify');
                $val .= $form->label(Language::title('modifyAPlayer'));
                $val .= $form->selectSubmit('id_player', $data);
                $val .= "</form>\n";
            }
        }
        return $val;
    }
    
    static function deletePopup($pdo, $teamId, $playerId){
        $req = '';
        $req .= "DELETE FROM teamOfTheWeek 
        WHERE id_player=:id_player;";
        $req .= "DELETE FROM season_team_player
        WHERE id_player=:id_player;";
        $req .= "DELETE FROM player
        WHERE id_player=:id_player;";
        $data = $pdo->prepare($req,[
            'id_player' => $playerId
        ]);
        $pdo->alterAuto('teamOfTheWeek');
        $pdo->alterAuto('season_team_player');
        $pdo->alterAuto('player');
        popup(Language::title('deleted'),"index.php?page=player");
    }
    
    static function createForm($pdo, $error, $form){
        $val = '';
        $val .= "<form action='index.php?page=player' method='POST'>\n";
        $val .= $form->inputAction('create');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('player')) . "</legend>\n";
        $val .= $error->getError();
        $val .= $form->input(Language::title('name'), 'name');
        $val .= "<br />\n";
        $val .= $form->input(Language::title('firstname'), 'firstname');
        $val .= "<br />\n";
        $val .= $form->inputRadioPosition();
        $val .= $form->selectTeam($pdo);
        $val .= "</fieldset>\n";
        $val .= "<br />\n";
        $val .= $form->submit(Language::title('create'));
        $val .= "</form>\n";
        return $val;
    }
    
    static function createPopup($pdo, $teamId, $playerId, $playerName, $playerFirstname, $playerPosition){
        
        $pdo->alterAuto('season_team_player');
        $pdo->alterAuto('player');
        $req1="INSERT INTO player
        VALUES(NULL,:name,:firstname,:position);";
        $pdo->prepare($req1,[
            'name' => $playerName,
            'firstname' => $playerFirstname,
            'position' => $playerPosition
        ]);
        $playerId=$pdo->lastInsertId();
        $req2="INSERT INTO season_team_player VALUES(NULL,:id_season,:id_team,:id_player);";
        $pdo->prepare($req2,[
            'id_season' => $_SESSION['seasonId'],
            'id_team' => $teamId,
            'id_player' => $playerId
        ]);
        popup(Language::title('created'),"index.php?page=player");
    }
    
    static function modifyForm($pdo, $error, $form, $playerId){
        
        // Table of seasons
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
        if($counter>0){
            $table = "<table>\n";
            $table .= "   <tr>\n";
            $table .= "       <th>" . (Language::title('season')) . "</th>\n";
            $table .= "       <th>" . (Language::title('team')) . "</th>\n";
            $table .= "   </tr>\n";
            foreach($data as $d){
                $table .= "   <tr>\n";
                $table .= "       <td>" . $d->season . "</td>\n";
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
        $val .= "<br />\n";
        $val .= $form->input(Language::title('firstname'),'firstname');
        $val .= "<br />\n";
        $val .= $form->inputRadioPosition($data);
        $val .= $form->selectTeam($pdo, null, $data->id_team);
        $val .= $table;
        $val .= "</fieldset>\n";
        $val .= "<br />\n";
        $val .= $form->submit(Language::title('modify'));
        $val .= "</form>\n";
        $val .= "<br />\n";
        $val .= $form->deleteForm('player', 'id_player', $playerId, false, 'id_team', $data->id_team);
        
        
        return $val;
    }
    
    static function modifyPopup($pdo, $teamId, $playerId, $playerName, $playerFirstname, $playerPosition){
        
        // Check if the player is known in Season Team Player table
        $req = "SELECT COUNT(*) as nb
        FROM season_team_player
        WHERE id_season=:id_season
        AND id_team=:id_team
        AND id_player=:id_player;";
        $data = $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId'],
            'id_team' => $teamId,
            'id_player' => $playerId
        ],true);
        $req="UPDATE player
        SET name=:name, firstname=:firstname, position=:position
        WHERE id_player=:id_player;";
        if($data->nb==0){
            $req.="INSERT INTO season_team_player
            VALUES(NULL,:id_season,:id_team,:id_player);";
        } else {
            $req.="UPDATE season_team_player SET id_season=:id_season,id_team=:id_team WHERE id_player=:id_player;";
        }
        $pdo->prepare($req,[
            'name' => $playerName,
            'firstname' => $playerFirstname,
            'position' => $playerPosition,
            'id_season' => $_SESSION['seasonId'],
            'id_team' => $teamId,
            'id_player' => $playerId
        ]);
        popup(Language::title('modified'),"index.php?page=player");
    }
    
    static function list($pdo){
        
        $req = "SELECT COUNT(e.rating) as nb,AVG(e.rating) as rating,c.name as team,j.name,j.firstname
        FROM player j
        LEFT JOIN season_team_player scj ON j.id_player=scj.id_player
        LEFT JOIN team c ON  c.id_team=scj.id_team
        LEFT JOIN teamOfTheWeek e ON e.id_player=j.id_player
        LEFT JOIN season_championship_team sct ON sct.id_team=scj.id_team 
        LEFT JOIN matchday md ON md.id_matchday = e.id_matchday 
        WHERE scj.id_season='".$_SESSION['seasonId']."'  
        AND sct.id_season='".$_SESSION['seasonId']."' 
        AND sct.id_championship='".$_SESSION['championshipId']."'  
        AND md.id_season ='".$_SESSION['seasonId']."'
        GROUP BY team, j.name,j.firstname
        ORDER BY nb DESC, rating DESC,j.name,j.firstname LIMIT 0,3";
        $data = $pdo->prepare($req,null,true);
        $val = "<p>\n";
        $val .= "  <table class='player'>\n";
        $val .= "      <tr><th></th><th>" . (Language::title('player')) . "</th><th>" . (Language::title('team')) . "</th><th>" . (Language::title('teamOfTheWeek')) . "</th><th>" . (Language::title('ratingAverage')) . "</th></tr>\n";
        $counterPodium = 0;
        $icon =  Theme::icon('medalGold'); // Gold medal
        $counter = $pdo->rowCount();
        if($counter>0){
            foreach ($data as $d)
            {
                $counterPodium++;
                if($counterPodium==2) $icon = Theme::icon('medalSilver'); // Silver medal
                else $icon = Theme::icon('medalBronze'); // Bronze medal
                
                $val .= "      <td><strong>".$counterPodium."</strong></td>\n";
                $val .= "      <td>".$icon." <strong>".mb_strtoupper($d->name,'UTF-8')." ".$d->firstname."</strong></td>\n";
                $val .= "      <td>".$d->team."</td>\n";
                $val .= "      <td>".$d->nb."</td>\n";
                $val .= "      <td>".round($d->rating,1)."</td>\n";
                $val .= "  </tr>\n";
            }
        } else {
            $val .= "        <tr>\n";
            $val .= "<td colspan='5'>" . Language::title('notPlayed') . "</td>\n";
            $val .= "        </tr>\n";
        }
        $val .= "  </table>\n";
        $val .= "</p>\n";
        
        
        $val .= "<h3>" . (Language::title('bestPlayersByTeam')) . "</h3>\n";
        
        $req = "SELECT COUNT(e.rating) as nb,AVG(e.rating) as rating,c.name as team,j.name,j.firstname
    FROM player j
    LEFT JOIN season_team_player scj ON j.id_player=scj.id_player
    LEFT JOIN team c ON  c.id_team=scj.id_team 
    LEFT JOIN teamOfTheWeek e ON e.id_player=j.id_player 
    LEFT JOIN matchday md ON md.id_matchday=e.id_matchday 
    WHERE   scj.id_season = :id_season
    AND md.id_season = :id_season 
    AND c.id_team IN (SELECT id_team FROM season_championship_team 
        WHERE id_season = :id_season AND id_championship = :id_championship)
    GROUP BY team,j.name,j.firstname
    ORDER BY team ASC, nb DESC, rating DESC, j.name,j.firstname ASC";
        $data = $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId'],
            'id_championship' => $_SESSION['championshipId']
        ],true);
        $val .= "  <table class='player'>\n";
        $val .= "      <tr><th>" . (Language::title('team')) . "</th><th>" . (Language::title('player')) . "</th><th>" . (Language::title('teamOfTheWeek')) . "</th><th>" . (Language::title('ratingAverage')) . "</th></tr>\n";
        $counter = "";
        $counterPlayers = $pdo->rowCount();
        if($counterPlayers>0){
            foreach ($data as $d)
            {
                $val .= "      <td>";
                if($counter!=$d->team){
                    $counterPodium = 0;
                    $val .= "<strong>".$d->team."</strong>";
                }
                
                $counterPodium++;
                switch($counterPodium){
                    case 1:
                        $icon = "&#129351;"; // gold medal
                        break;
                    case 2:
                        $icon="&#129352;"; // silver medal
                        break;
                    case 3:
                        $icon="&#129353;"; // bronze medal
                        break;
                    default:
                        $icon="";
                }
                
                $val .= "</td><td>";
                if($icon != '') $val .= $icon." <strong>".mb_strtoupper($d->name,'UTF-8')." ".$d->firstname."</strong>";
                else $val .= mb_strtoupper($d->name,'UTF-8')." ".$d->firstname;
                $val .= "</td><td>".$d->nb."</td><td>".round($d->rating,1)."</td>\n";
                $val .= "  </tr>\n";
                $counter=$d->team;
            }
        } else {
            $val .= "        <tr>\n";
            $val .= "<td colspan='4'>" . Language::title('notPlayed') . "</td>\n";
            $val .= "        </tr>\n";
        }
        $val .= "  </table>\n";
        return $val;
    }
}
?>