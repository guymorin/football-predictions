<?php 
/**
 * 
 * Class Championship Popup
 * Manage popups championship page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Theme;
use FootballPredictions\Statistics;

class ChampionshipPopup
{
    public function __construct(){

    }    

    static function createPopup($pdo, $championshipName){
        $pdo->alterAuto('championship');
        $req="INSERT INTO championship
        VALUES(NULL,:name);";
        $pdo->prepare($req,[
            'name' => $championshipName
        ]);
        popup(Language::title('created'),"index.php?page=championship");
    }
    
    static function deletePopup($pdo, $championshipId){
        $req = '';
        $req .= "DELETE FROM teamOfTheWeek WHERE id_matchday IN (
            SELECT id_matchday FROM matchday WHERE id_championship=:id_championship);";
        $req .= "DELETE FROM criterion WHERE id_matchgame IN (
            SELECT id_matchgame FROM matchgame WHERE id_matchday IN (
                SELECT id_matchday FROM matchday WHERE id_championship=:id_championship));";
        $req .= "DELETE FROM matchgame WHERE id_matchday IN (
            SELECT id_matchday FROM matchday WHERE id_championship=:id_championship);";
        $req .= "DELETE FROM matchday WHERE id_championship=:id_championship;";
        $req .= "DELETE FROM season_championship_team WHERE id_championship=:id_championship;";
        $req .= "DELETE FROM championship WHERE id_championship=:id_championship;";
        $req .= "UPDATE fp_user SET last_season = NULL, last_championship = NULL WHERE last_championship=:id_championship;";
        
        $pdo->prepare($req,[
            'id_championship' => $championshipId
        ]);
        $pdo->alterAuto('teamOfTheWeek');
        $pdo->alterAuto('criterion');
        $pdo->alterAuto('matchgame');
        $pdo->alterAuto('matchday');
        $pdo->alterAuto('season_championship_team');
        $pdo->alterAuto('championship');
        popup(Language::title('deleted'),"index.php?page=championship");
    }
    
    static function selectMultiPopup($pdo, $teams){
        $pdo->alterAuto('season_championship_team');
        $req = '';
        foreach($teams as $t){
            $req .= "INSERT INTO season_championship_team VALUES(NULL, "
                . $_SESSION['seasonId'] . ","
                . $_SESSION['championshipId'] . "," 
                . $t . ");";
        }
        $pdo->exec($req);
        popup(Language::title('selected'),"index.php?page=championship");
    }
    
    static function modifyPopup($pdo, $championshipName, $championshipId){
        
        $req="UPDATE championship
        SET name=:name
        WHERE id_championship=:id_championship;";
        $pdo->prepare($req,[
            'name' => $championshipName,
            'id_championship' => $championshipId
        ]);
        popup(Language::title('modified'),"index.php?page=championship");
    }
}
?>