<?php 
/**
 * 
 * Class Matchgame Popup
 * Manage popups in matchgame page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Statistics;
use FootballPredictions\Theme;

class MatchgamePopup
{
    public function __construct(){

    }
     
    static function createPopup($pdo, $team1, $team2, $result, $odds1, $oddsD, $odds2, $date){        
        $pdo->alterAuto('matchgame');
        $req="INSERT INTO matchgame
            VALUES(NULL,'".$_SESSION['matchdayId']."','".$team1."','".$team2."',".$result.",'".$odds1."','".$oddsD."','".$odds2."','".$date."',0,0);";
        $pdo->exec($req);
        popup(Language::title('created'),"index.php?page=matchgame&create=1");
    }
    static function deletePopupMatch($pdo, $idMatchgame){
        $req = '';
        $req .= "DELETE FROM criterion WHERE id_matchgame=:id_matchgame;";
        $req .= "DELETE FROM matchgame WHERE id_matchgame=:id_matchgame;";
        $pdo->prepare($req,[
            'id_matchgame' => $idMatchgame
        ]);
        $pdo->alterAuto('criterion');
        $pdo->alterAuto('matchgame');
        popup(Language::title('deleted'),"index.php?page=matchgame");
    }
    
    static function modifyPopup($pdo, $team1, $team2, $result, $odds1, $oddsD, $odds2, $date, $idMatch){
        
        $req="UPDATE matchgame
            SET id_matchday = :id_matchday, team_1=:team_1, team_2 = :team_2, result = :result, odds1= :odds1, oddsD= :oddsD, odds2= :odds2, date= :date 
            WHERE id_matchgame = :id_matchgame;";
        $pdo->prepare($req,[
            'id_matchday' => $_SESSION['matchdayId'],
            'team_1' => $team1,
            'team_2' => $team2,
            'result' => $result,
            'odds1' => $odds1,
            'oddsD' => $oddsD,
            'odds2' => $odds2,
            'date' => $date,
            'id_matchgame' => $idMatch
        ]);
        popup(Language::title('modifyAMatch'),"index.php?page=matchgame");
    }
}
?>