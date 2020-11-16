<?php 
/**
 * 
 * Class Team
 * Manage Team page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Theme;
use \PDO;

class Team
{
    public function __construct(){

    }
    
    static function submenu($pdo, $form, $current=null){
        $val ='';
        $currentClass = " class='current'";
        $classMV = $classC = '';
        switch($current){
            case 'marketValue':
                $classMV = $currentClass;
                break;
            case 'create':
                $classC = $currentClass;
                break;
            case '':break;
        }
        $val .= "  	<a href='index.php?page=matchday'>" . (Theme::icon('matchday')) . " " . (Language::title('matchdays')) . "</a>";
        $val .= "<a" . $classMV . " href='index.php?page=team'>" . (Language::title('marketValue')) . "</a>";
        if(($_SESSION['role'])==2){           
            $val .= "<a" . $classC . " href='index.php?page=team&create=1'>" . (Language::title('createATeam')) . "</a>\n";
            $req = "SELECT * FROM team c ORDER BY name;";
            $data = $pdo->query($req);
            $counter = $pdo->rowCount();
            if($counter > 1){
                $val .= "<form action='index.php?page=team' method='POST'>\n";
                $val .= $form->inputAction('modify');
                $val .= $form->label(Language::title('modifyATeam'));
                $val .= $form->selectSubmit('id_team', $data);
                $val .= "</form>\n";
            }
        }
        $val .= "<a href='index.php?page=player'>" . (Theme::icon('player')) . " " . (Language::title('player')) . "</a>";        
        return $val;
    }
    
    static function deletePopup($pdo, $teamId){
        $req = '';
        $req .= "DELETE FROM marketValue WHERE id_team=:id_team;";
        $req .= "DELETE FROM matchgame WHERE team_1=:id_team;";
        $req .= "DELETE FROM matchgame WHERE team_2=:id_team;";
        $req .= "DELETE FROM season_championship_team WHERE id_team=:id_team;";
        $req .= "DELETE FROM season_team_player WHERE id_team=:id_team;";
        $req .= "DELETE FROM team WHERE id_team=:id_team;";
        $pdo->prepare($req,[
            'id_team' => $teamId
        ]);
        $pdo->alterAuto('marketValue');
        $pdo->alterAuto('matchgame');
        $pdo->alterAuto('season_championship_team');
        $pdo->alterAuto('season_team_player');
        $pdo->alterAuto('team');
        popup(Language::title('deleted'),"index.php?page=team");
    }
    
    static function createForm($pdo, $error, $form, $teamName, $weatherCode){    
        $val = '';
        $val .= "<form action='index.php?page=team' method='POST'>\n";
        $val .= $form->inputAction('create');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('team')) . "</legend>\n";
        $val .= $error->getError();
        $form->setValue('name', $teamName);
        $form->setValue('weather_code', $weatherCode);
        $val .= $form->input(Language::title('name'), 'name');
        $val .= "<br />\n";
        $val .= $form->input(Language::title('weathercode'), 'weather_code');
        $val .= "</fieldset>\n";
        $val .= "<br />\n";
        $val .= $form->submit(Language::title('create'));
        $val .= "</form>\n";  
        return $val;
    }
    
    static function createPopup($pdo, $teamName, $weatherCode){
        $pdo->alterAuto('team');
        $req="INSERT INTO team VALUES(NULL, '" . $teamName . "', $weatherCode);";
        $pdo->exec($req);
        popup(Language::title('created'),"index.php?page=team");
    }
    
    static function modifyForm($pdo, $error, $form, $teamId){  
        $req = "SELECT * FROM team WHERE id_team=:id_team;";
        $data = $pdo->prepare($req,[
            'id_team' => $teamId
        ]);
        $val = '';
        $val .= "<form action='index.php?page=team' method='POST'>\n";
        $form->setValues($data);
        $val .= $form->inputAction('modify');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('team')) . "</legend>\n";
        $val .= $error->getError();
        $val .= $form->inputHidden('id_team',$teamId);
        $val .= $form->input(Language::title('name'), 'name');
        $val .= "<br />\n";
        $val .= $form->input(Language::title('weathercode'), 'weather_code');
        $val .= "</fieldset>\n";
        $val .= "<br />\n";
        $val .= $form->submit(Language::title('modify'));
        $val .= "</form>\n";
        $val .= "<br />\n";
        $req = "SELECT * FROM season_championship_team 
        WHERE id_season = :id_season 
        AND id_championship = :id_championship 
        AND id_team = :id_team;";
        $data = $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId'],
            'id_championship' => $_SESSION['championshipId'],
            'id_team' => $teamId
        ],true);
        $counter = $pdo->rowCount();
        if($counter==0){
            $val .= "<form action='index.php?page=team' method='POST'>\n";
            $val .= $form->inputAction('add');
            $val .= $form->inputHidden('id_team',$teamId);
            $val .= $form->submit(Language::title('addTeamTo').$_SESSION['championshipName'].' '.$_SESSION['seasonName']);
            $val .= "</form>\n";
            $val .= "<br />\n";
        }

        $val .= $form->deleteForm('team', 'id_team', $teamId);
        return $val;
    }
    
    static function addPopup($pdo, $teamId){
        $pdo->alterAuto('team');
        $req="INSERT INTO season_championship_team VALUES(NULL, '" . $_SESSION['seasonId'] . "', '" . $_SESSION['championshipId'] . "', '" . $teamId . "');";
        $pdo->exec($req);
        popup(Language::title('added'),"index.php?page=team");
    }
    
    static function modifyFormMarketValue($pdo, $error, $form){
        $val = '';
        $req = "SELECT DISTINCT c.*, v.marketValue
        FROM team c
        LEFT JOIN marketValue v ON v.id_team=c.id_team
        LEFT JOIN season_championship_team scc ON scc.id_team=c.id_team 
        WHERE scc.id_season=:id_season
        AND scc.id_championship=:id_championship 
        AND v.id_season=:id_season 
        ORDER BY c.name;";
        $data = $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId'],
            'id_championship' => $_SESSION['championshipId']
        ],true);
        $counter = $pdo->rowCount();
        if($counter > 0){
            if(($_SESSION['role'])==2){ 
                $val = $error->getError();
                $val .= "<form action='index.php?page=team' method='POST'>\n";
                $form->setValues($data);
                $val .= $form->label(Language::title('modifyAMarketValue'));
            }
            $val .= "<table class='team'>\n";
            $val .= "  <tr>\n";
            $val .= "      <th>" . (Language::title('team')) . "</th>\n";
            $val .= "      <th>" . (Language::title('marketValue')) . "</th>\n";
            $val .= "  </tr>\n";
            foreach ($data as $d)
            {
                $val .= "  <tr>\n";
                $val .= "      <td>\n";
                if(($_SESSION['role'])==2) $val .= $form->inputHidden('id_team[]', $d->id_team);
                $val .= Theme::icon('team') . " " . $d->name;
                $val .= "      </td>\n";
                $val .= "      <td>\n";
                if(($_SESSION['role'])==2){ 
                    $form->setValue('marketValue',$d->marketValue);
                    $val .= $form->input('', 'marketValue[]');
                } else $val.= $d->marketValue;
                $val .= "      </td>\n";
                $val .= "  </tr>\n";
            }
            $val .= "</table>\n";
            if(($_SESSION['role'])==2){ 
                $val .= $form->submit(Language::title('modify'));
            }
        } else $val .= Language::title('noTeam');
        return $val;
    }
    
    static function modifyPopup($pdo, $teamName, $weatherCode, $teamId){
        
        $req="UPDATE team SET name = '" . $teamName . "', weather_code = '" . $weatherCode . "' 
        WHERE id_team = '" . $teamId . "';";
        $pdo->exec($req);
        popup(Language::title('modified'),"index.php?page=team");
    }
    
    static function modifyPopupMarketValue($pdo, $error, $val){
        
        $pdo->alterAuto('marketValue');
        $req = "";
        foreach($val as $k=>$v){
            $v=$error->check('Digit', $v, Language::title('marketValue'));
            if($v>0){
                $r = "SELECT COUNT(*) as nb FROM marketValue
                WHERE id_season=:id_season AND id_team=:id_team;";
                $data = $pdo->prepare($r,[
                    'id_season' => $_SESSION['seasonId'],
                    'id_team' => $k
                ]);
                
                if($data->nb==0){
                    $req .= "INSERT INTO marketValue VALUES(NULL,'".$_SESSION['seasonId']."','".$k."','".$v."');";
                }
                if($data->nb==1){
                    $req .= "UPDATE marketValue SET marketValue='".$v."' WHERE id_season='".$_SESSION['seasonId']."' AND id_team='".$k."';";
                }
            }
        }
        
        $pdo->exec($req);
        popup(Language::title('modified'),"index.php?page=team");
    }
}
?>