<?php 
/**
 * 
 * Class Team Popup
 * Manage popups in team page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Theme;
use \PDO;

class TeamPopup
{
    public function __construct(){

    }
    
    static function addPopup($pdo, $teamId){
        $pdo->alterAuto('team');
        $req="INSERT INTO season_championship_team VALUES(NULL, '" . $_SESSION['seasonId'] . "', '" . $_SESSION['championshipId'] . "', '" . $teamId . "');";
        $pdo->exec($req);
        popup(Language::title('added'),"index.php?page=team");
    }
    
    static function createPopup($pdo, $teamName, $weatherCode){
        $pdo->alterAuto('team');
        $req="INSERT INTO team VALUES(NULL, '" . $teamName . "', $weatherCode);";
        $pdo->exec($req);
        popup(Language::title('created'),"index.php?page=team");
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