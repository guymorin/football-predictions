<?php 
/**
 * 
 * Class Matchgame
 * Manage Matchgame page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Statistics;
use FootballPredictions\Theme;

class Matchgame
{
    public function __construct(){

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
    
    static function createPopup($pdo, $team1, $team2, $result, $odds1, $oddsD, $odds2, $date){
        
        $pdo->alterAuto('matchgame');
        $req="INSERT INTO matchgame
            VALUES(NULL,'".$_SESSION['matchdayId']."','".$team1."','".$team2."','".$result."','".$odds1."','".$oddsD."','".$odds2."','".$date."',0,0);";
        $pdo->exec($req);
        popup(Language::title('created'),"index.php?page=matchgame&create=1");
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
    
    static function createForm($pdo, $error, $form){
        
        $val = '';
        $val .= "<form action='index.php?page=matchgame' method='POST'>\n";
        $val .= $form->inputAction('create');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('matchgame')) . "</legend>\n";
        $val .= $error->getError();
        $val .= $form->inputHidden('matchdayId', $_SESSION['matchdayId']);
        
        $val .= $form->inputDate(Language::title('date'), 'date', '');
        $val .= "<br />";
        
        $req="SELECT c.id_team, c.name FROM team c
            LEFT JOIN season_championship_team scc ON c.id_team=scc.id_team
            WHERE scc.id_season=:id_season AND scc.id_championship=:id_championship ORDER BY c.name;";
        $data = $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId'],
            'id_championship' => $_SESSION['championshipId']
        ],true);
        $val .= $form->selectTeam($pdo,'team_1');
        $val .= $form->selectTeam($pdo,'team_2');
        $val .= "<br />";
        $val .= $form->inputNumberOdds();
        $val .= "<br />";
        $val .= $form->inputRadioResult();
        $val .= "</fieldset>\n";
        $val .= "<fieldset>\n";
        $val .= $form->submit(Theme::icon('create')." ".Language::title('create'));
        $val .= "</form>\n";   
        $val .= "</fieldset>\n";
        return $val;
    }
    
    static function modifyForm($pdo, $error, $form, $idMatch){
        
        $req="SELECT m.id_matchgame,c1.name as name1,c2.name as name2,c1.id_team as id1,c2.id_team as id2, m.result, m.date, m.odds1, m.oddsD, m.odds2
            FROM matchgame m LEFT JOIN team c1 ON m.team_1=c1.id_team LEFT JOIN team c2 ON m.team_2=c2.id_team
            WHERE m.id_matchgame = $idMatch;";
        $data = $pdo->queryObj($req);
        $val = '';
        $val .= "<form action='index.php?page=matchgame' method='POST'>\n";
        $form->setValues($data);
        $val .= $form->inputAction('modify');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('matchday')) . "</legend>\n";
        $val .= $error->getError();
        $val .= $form->inputHidden('id_matchgame',$data->id_matchgame);
        $val .= $form->inputDate(Language::title('date'), 'date', $data->date);
        $val .= "<br />";
        $val .= $form->selectTeam($pdo,'team_1',$data->id1);
        $val .= $form->selectTeam($pdo,'team_2',$data->id2);
        $val .= "<br />";
        $val .= $form->inputNumberOdds($data);
        $val .= "<br />";
        $val .= $form->inputRadioResult($data);
        $val .= "</fieldset>\n";
        $val .= "<fieldset>\n";
        $val .= $form->submit(Theme::icon('modify')." ".Language::title('modify'));
        $val .= "</form>\n";
        $val .= $form->deleteForm('matchgame', 'id_matchgame', $idMatch);
        $val .= "</fieldset>\n";
        return $val;
    }
       
}
?>