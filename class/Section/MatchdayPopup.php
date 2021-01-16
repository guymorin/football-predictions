<?php 
/**
 * 
 * Class Matchday Popup
 * Manage popups on matchday page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Statistics;
use FootballPredictions\Theme;
use FootballPredictions\Forms;

class MatchdayPopup
{
    public function __construct(){

    }
        
    static function createPopup($pdo, $matchdayNumber){
        
        $pdo->alterAuto('matchday');
        $req = "INSERT INTO matchday
        VALUES(NULL,:id_season,:id_championship,:number);";
        $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId'],
            'id_championship' => $_SESSION['championshipId'],
            'number' => $matchdayNumber
        ]);
        popup(Language::title('created'),"index.php?page=matchday");
    }
    
    static function createMultiPopup($pdo, $matchdayTotalNumber){
        
        $pdo->alterAuto('matchday');
        $req = '';
        for($i=1;$i<=$matchdayTotalNumber;$i++){
            $req .= "INSERT INTO matchday
            VALUES(NULL,:id_season,:id_championship,$i);";            
        }
        $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId'],
            'id_championship' => $_SESSION['championshipId'],
        ]);
        $_SESSION['noMatchday'] = false;
        popup(Language::title('created'),"index.php?page=matchday");
    }
    
    static function deletePopup($pdo, $matchdayId){
        $req = '';
        $req .= "DELETE FROM teamOfTheWeek WHERE id_matchday='".$matchdayId."';";
        $req .= "DELETE FROM criterion WHERE id_matchgame IN (
            SELECT id_matchgame FROM matchgame WHERE id_matchday='".$matchdayId."');";
        $req .= "DELETE FROM matchgame WHERE id_matchday='".$matchdayId."';";
        $req .= "DELETE FROM matchday WHERE id_matchday='".$matchdayId."';";
        $pdo->exec($req);
        $pdo->alterAuto('teamOfTheWeek');
        $pdo->alterAuto('criterion');
        $pdo->alterAuto('matchgame');
        $pdo->alterAuto('matchday');
        popup(Language::title('deleted'),"index.php?page=matchday");
    }
    
    static function modifyPopup($pdo, $matchdayNumber, $matchdayId){
        
        $req="UPDATE matchday
        SET number=:number
        WHERE id_matchday=:id_matchday;";
        $pdo->prepare($req,[
            'number' => $matchdayNumber,
            'id_matchday' => $matchdayId
        ]);
        popup(Language::title('modified'),"index.php?page=matchday");
    }
}
?>