<?php 
/**
 * 
 * Class Season Popup
 * Manage popups in season page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Theme;

class SeasonPopup
{
    public function __construct(){

    }   

    static function createPopup($pdo, $seasonName){
        
        $pdo->alterAuto('season');
        $req="INSERT INTO season VALUES(NULL,:name);";
        $pdo->prepare($req,[
            'name' => $seasonName
        ]);
        popup(Language::title('created'),"index.php?page=season");
    }
    static function deletePopup($pdo, $seasonId){
        
        $req .= "DELETE FROM teamOfTheWeek WHERE id_matchday IN (
            SELECT id_matchday FROM matchday WHERE WHERE id_season='".$seasonId."');";
        $req .= "DELETE FROM criterion WHERE id_matchgame IN (
            SELECT id_matchgame FROM matchgame WHERE id_matchday IN (
                SELECT id_matchday FROM matchday WHERE WHERE id_season='".$seasonId."'));";
        $req .= "DELETE FROM matchgame WHERE id_matchday IN (
            SELECT id_matchday FROM matchday WHERE WHERE id_season='".$seasonId."');";
        $req="DELETE FROM matchday WHERE id_season=$seasonId;";
        $req="DELETE FROM marketValue WHERE id_season=$seasonId;";
        $req="DELETE FROM season_team_player WHERE id_season=$seasonId;";
        $req="DELETE FROM season_championship_team WHERE id_season=$seasonId;";
        $req="DELETE FROM season WHERE id_season=$seasonId;";
        $pdo->exec($req);
        $pdo->alterAuto('teamOfTheWeek');
        $pdo->alterAuto('criterion');
        $pdo->alterAuto('matchgame');
        $pdo->alterAuto('matchday');
        $pdo->alterAuto('marketValue');
        $pdo->alterAuto('season_team_player');
        $pdo->alterAuto('season_championship_team');
        $pdo->alterAuto('season');
        popup(Language::title('deleted'),"index.php?page=season");
    }
    
    
    static function modifyPopup($pdo, $seasonName, $seasonId){
        
        $req="UPDATE season
        SET name=:name
        WHERE id_season=:id_season;";
        $pdo->prepare($req,[
            'name' => $seasonName,
            'id_season' => $seasonId
        ]);
        popup(Language::title('modified'),"index.php?page=season");
    }

}
?>