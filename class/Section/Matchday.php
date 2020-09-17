<?php 
/**
 * 
 * Class Matchday
 * Manage Matchday page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Statistics;
use FootballPredictions\Theme;

class Matchday
{
    public function __construct(){

    }

    static function exitButton() {
        
        if(isset($_SESSION['matchdayId'])){
            echo "<a class='session' href='index.php?page=matchday&exit=1'>" . (Language::title('MD')) . $_SESSION['matchdayNum'] . " &#10060;</a>";
        }
    }
    
    static function submenu($pdo, $form, $current = null){
        $val = "<a href='index.php?page=championship'>" . (Theme::icon('championship')) . " ";
        if(isset($_SESSION['championshipId']) && $_SESSION['championshipId']>0) {
            $val .= $_SESSION['championshipName'];
        } else {
            $val .= Language::title('championship');
        }
        $val .= "</a>";
        $currentClass = " class='current'";
        $classS = $classP = $classR = $classTOTW = $classCM = $classLMD = $classCMD = '';
        switch($current){
            case 'statistics':
                $classS = $currentClass;
                break;
            case 'prediction':
                $classP = $currentClass;
                break;
            case 'results':
                $classR = $currentClass;
                break;
            case 'teamOfTheWeek':
                $classTOTW = $currentClass;
                break;
            case 'createMatch':
                $classCM = $currentClass;
                break;
            case 'create':
                $classCMD = $currentClass;
                break;
            case 'list':
                $classLMD = $currentClass;
                break;
        }
        if(isset($_SESSION['matchdayId'])){ 
            $val .= "<a" . $classLMD . " href='index.php?page=matchday'>" . (Language::title('listMatchdays')) . "</a>";
            $val .= "<a" . $classS . " href='index.php?page=statistics'>" . (Language::title('statistics')) . "</a>";
            $val .= "<a" . $classP . " href='index.php?page=prediction'>" . (Language::title('predictions')) . "</a>";
            $val .= "<a" . $classR . " href='index.php?page=results'>" . (Language::title('results')) . "</a>";
            $val .= "<a" . $classTOTW . " href='index.php?page=teamOfTheWeek'>" . (Language::title('teamOfTheWeek')) . "</a>";
            $val .= "<a" . $classCM . " href='index.php?page=matchgame&create=1'>" . (Language::title('createAMatch')) . "</a>";
            $req = "SELECT DISTINCT mg.id_matchgame, t1.name, t2.name, mg.date
            FROM matchgame mg
            LEFT JOIN matchday md ON md.id_matchday = mg.id_matchday  
            LEFT JOIN team t1 ON mg.team_1 = t1.id_team 
            LEFT JOIN team t2 ON mg.team_2 = t2.id_team 
            WHERE md.id_season = " . $_SESSION['seasonId'] . "
            AND md.id_championship = " . $_SESSION['championshipId'] . " 
            AND md.id_matchday = " . $_SESSION['matchdayId'] . " ORDER BY mg.date;";
            $data = $pdo->query($req);
            $counter = $pdo->rowCount();
            if($counter > 0){
                $val .= "<form action='index.php?page=matchgame' method='POST'>\n";
                $val .= $form->inputAction('modify');
                $val .= $form->label(Language::title('modifyAMatch'));
                $val .= $form->selectSubmit('id_matchgame', $data);
                $val .= "</form>\n";
            }
        } else {
            $val .= "<a" . $classLMD . " href='index.php?page=matchday'>" . (Language::title('listMatchdays')) . "</a>";
            $val .= "<a" . $classCMD . " href='index.php?page=matchday&create=1'>";
            if ($_SESSION['noMatchday'] == true) $val .= Language::title('createTheMatchdays');
            else  $val .= Language::title('createAMatchday');
            $val .= "</a>\n";
            if(($_SESSION['role'])==2){
                $req = "SELECT DISTINCT id_matchday, number FROM matchday
                WHERE id_season = " . $_SESSION['seasonId'] . "
                AND id_championship = " . $_SESSION['championshipId'] . " ORDER BY number DESC;";
                $data = $pdo->query($req);
                $counter = $pdo->rowCount();
                if($counter > 0){
                    $val .= "<form action='index.php?page=matchday' method='POST'>\n";
                    $val .= $form->inputAction('modify');
                    $val .= $form->label(Language::title('modifyAMatchday'));
                    $val .= $form->selectSubmit('matchdayModify', $data);
                    $val .= "</form>\n";
                }
            }
        }
        return $val;
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
    
    static function deletePopupMatch($pdo, $idMatch){
        $req = '';
        $req .= "DELETE FROM criterion WHERE id_matchgame=:id_matchgame;";
        $req .= "DELETE FROM matchgame WHERE id_matchgame=:id_matchgame;";
        $pdo->prepare($req,[
            'id_matchgame' => $idMatch
        ]);
        $pdo->alterAuto('criterion');
        $pdo->alterAuto('matchgame');
        popup(Language::title('deleted'),"index.php?page=matchgame");
    }
    
    static function createForm($pdo, $error, $form){
        
        $val = '';
        $val .= "<form action='index.php?page=matchday' method='POST'>\n";
        $val .= $form->inputAction('create');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('matchday')) . "</legend>\n";
        $val .= $error->getError();
        $val .= $form->input(Language::title('number'),'number');
        $val .= "</fieldset>\n";
        $val .= "<br />\n";
        $val .= $form->submit(Language::title('create'));
        $val .= "</form>\n";
        return $val;
    }
    
    static function createMultiForm($pdo, $error, $form){
        
        $val = '';
        $val .= "<form action='index.php?page=matchday&create=1' method='POST'>\n";
        $val .= $form->inputAction('createMulti');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('matchdays')) . "</legend>\n";
        $val .= $error->getError();
        $val .= $form->input(Language::title('matchdayNumber'),'totalNumber');
        $val .= "</fieldset>\n";
        $val .= "<br />\n";
        $val .= $form->submit(Language::title('create'));
        $val .= "</form>\n";
        return $val;
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
    
    static function createPopupMatch($pdo, $team1, $team2, $result, $odds1, $oddsD, $odds2, $date){
        
        $pdo->alterAuto('matchgame');
        $req="INSERT INTO matchgame
            VALUES(NULL,'".$_SESSION['matchdayId']."','".$team1."','".$team2."','".$result."','".$odds1."','".$oddsD."','".$odds2."','".$date."',0,0);";
        $pdo->exec($req);
        popup(Language::title('created'),"index.php?page=matchgame&create=1");
    }
    
    static function modifyForm($pdo, $matchdayId, $error, $form){
        
        $req = "SELECT * FROM matchday WHERE id_matchday=:id_matchday;";
        $data = $pdo->prepare($req,[
            'id_matchday' => $matchdayId
        ]);
        
        $val = '';
        $val .= " <form action='index.php?page=matchday' method='POST';'>\n";
        $form->setValues($data);
        $val .= $form->inputAction('modify');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('matchday')) . "</legend>\n";
        $val .= $error->getError();
        $val .= $form->inputHidden("id_matchday", $data->id_matchday);
        $val .= $form->input(Language::title('number'), "number");
        $val .= "</fieldset>\n";
        $val .= "<br />\n";
        $val .= $form->submit(Language::title('modify'));
        $val .= " </form>\n";
        $val .= "<br />\n";
        $val .= $form->deleteForm('matchday', 'id_matchday', $matchdayId);
        return $val;
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
    
    static function modifyPopupMatch($pdo, $team1, $team2, $result, $odds1, $oddsD, $odds2, $date, $idMatch){
        
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
    
    static function createMatchForm($pdo, $error, $form){
        
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
        $val .= "<br />";
        $val .= $form->submit(Language::title('create'));
        $val .= "</form>\n";   
        return $val;
    }
    
    static function modifyMatchForm($pdo, $error, $form, $idMatch){
        
        $req="SELECT m.id_matchgame,c1.name as name1,c2.name as name2,c1.id_team as id1,c2.id_team as id2, m.result, m.date, m.odds1, m.oddsD, m.odds2
            FROM matchgame m LEFT JOIN team c1 ON m.team_1=c1.id_team LEFT JOIN team c2 ON m.team_2=c2.id_team
            WHERE m.id_matchgame = $idMatch;";
        $data = $pdo->queryObj($req);
        $val = '';
        $val .= "<form action='index.php?page=matchgame' method='POST'>\n";
        $form->setValues($data);
        $val .= $form->inputAction('modify');
        $val .= "<fieldset>\n";
        $val .= "<legend>" . (Language::title('match')) . "</legend>\n";
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
        $val .= "<br />";
        $val .= $form->submit(Language::title('modify'));
        $val .= "</form>\n";
        $val .= "<br />\n";
        $val .= $form->deleteForm('matchgame', 'id_matchgame', $idMatch);
        
        return $val;
    }
    
    static function stats($pdo){
        changeMD($pdo,"statistics");
        $val = "<h3>" . (Language::title('statistics')) . "</h3>";
        $stats = new Statistics();
        $val .= $stats->getStats($pdo, 'matchday');
        return $val;
    }
    
    static function list($pdo, $form){
        
        $req = "SELECT md.id_matchday, md.number as number, COUNT(id_matchgame) as nb, COUNT(CASE WHEN mg.result IN('1','D','2') THEN 1 END) as played
        FROM matchday md
        LEFT JOIN matchgame mg ON mg.id_matchday=md.id_matchday
        WHERE md.id_season = :id_season 
        AND md.id_championship = :id_championship 
        GROUP BY md.id_matchday, md.number
        ORDER BY md.number";
        $data = $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId'],
            'id_championship' => $_SESSION['championshipId']
        ],true);
        $val = "<table>\n";
        $val .= "  <tr>\n";
        $val .= "      <th>" . (Language::title('matchday')) . "</th>\n";
        $val .= "      <th>" . (Language::title('matchNumber')) . "</th>\n";
        $val .= "      <th>" . (Language::title('matchPlayed')) . "</th>\n";
        $val .= "  </tr>\n";
        
        $counter = $pdo->rowCount();
        if($counter>0){
            foreach ($data as $d)
            {
                if(isset($_SESSION['matchdayNum']) && $d->number==$_SESSION['matchdayNum']) {
                    $val .= "  <tr class='current'>\n";
                    $val .= "<form>\n";
                    $val .= "<td>";
                    $val .= "<button disabled style='cursor:default' type='submit' value='".((Language::title('MD')) . ($d->number))."'>" . (Theme::icon('matchday') . " " . (Language::title('MD')) . ($d->number)) . "</button>";
                    $val .= "</td>\n";
                }
                else {
                    $val .= "  <tr>\n";
                    $val .= "<form id='" . ($d->id_matchday) . "' action='index.php?page=matchday' method='POST'>\n";
                    $val .= $form->inputHidden("matchdaySelect", $d->id_matchday . "," . $d->number);
                    $val .= "<td>";
                    $val .= "<button type='submit' value='".((Language::title('MD')) . ($d->number))."'>" . (Theme::icon('matchday') . " " . (Language::title('MD')) . ($d->number)) . "</button>";
                    $val .= "</td>\n";
                }
                $val .= "      <td>" . $d->nb . "</td>\n";
                $val .= "      <td>" . $d->played . "</td>\n";
                $val .= "</form>\n";
                $val .= "  </tr>\n";
            }            
        } else {
            $val .= "  <tr>\n";
            $val .= "      <td colspan='3'>" . Language::title('noMatchday') . " / " . Language::title('noMatch') . "</td>\n";
            $val .= "  </tr>\n";
        }

        $val .= "</table>\n";
        return $val;
    }
    
    
}
?>