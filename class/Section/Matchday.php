<?php 
/**
 * 
 * Class Matchday
 * Manage Matchday page
 */
namespace FootballPredictions\Section;
use \PDO;

class Matchday
{
    public function __construct(){

    }
    
    static function submenu($pdo, $form, $current = null){
        require '../lang/fr.php';
        $val = "  	<a href='/'>$title_homepage</a>";
        if(isset($_SESSION['matchdayId'])){
            
            if($current == 'statistics'){
                $val .= "<a class='current' href='index.php?page=matchday'>$title_statistics</a>";
            } else {
                $val .= "<a href='index.php?page=matchday'>$title_statistics</a>";
            }
            if($current == 'prediction'){ 
                $val .= "<a class='current' href='index.php?page=prediction'>$title_predictions</a>";
            } else {    
                $val .= "<a href='index.php?page=prediction'>$title_predictions</a>";
            }
            if($current == 'results'){
                $val .= "<a class='current' href='index.php?page=results'>$title_results</a>";
            } else {
                $val .= "<a href='index.php?page=results'>$title_results</a>";
            }
            if($current == 'teamOfTheWeek'){
                $val .= "<a class='current' href='index.php?page=teamOfTheWeek'>$title_teamOfTheWeek</a>";
            } else {
                $val .= "<a href='index.php?page=teamOfTheWeek'>$title_teamOfTheWeek</a>";
            }
            if($current == 'createMatch'){
                $val .= "<a class='current' href='index.php?page=match&create=1'>$title_createAMatch</a>";
            } else {
                $val .= "<a href='index.php?page=match&create=1'>$title_createAMatch</a>";
            }
            
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
            if($counter > 1){
                $val .= "<form action='index.php?page=match' method='POST'>\n";
                $val .= $form->inputAction('modify');
                $val .= $form->label($title_modifyAMatch);
                $val .= $form->selectSubmit('id_match', $data);
                $val .= "</form>\n";
            }
        } else {
            $val .= "<a href='index.php?page=matchday&create=1'>$title_createAMatchday</a>\n";
            $req = "SELECT DISTINCT id_matchday, number FROM matchday
            WHERE id_season = " . $_SESSION['seasonId'] . "
            AND id_championship = " . $_SESSION['championshipId'] . " ORDER BY number DESC;";
            $data = $pdo->query($req);
            $counter = $pdo->rowCount();
            if($counter > 1){
                $val .= "<form action='index.php?page=matchday' method='POST'>\n";
                $val .= $form->inputAction('modify');
                $val .= $form->label($title_modifyAMatchday);
                $val .= $form->selectSubmit('id_matchday', $data);
                $val .= "</form>\n";
            }
        }
        return $val;
    }
    
    static function deletePopup($pdo, $matchdayId){
        require '../lang/fr.php';
        $req="DELETE FROM matchday WHERE id_matchday='".$matchdayId."';";
        $pdo->exec($req);
        $pdo->alterAuto('matchday');
        popup($title_deleted,"index.php?page=matchday");
    }
    
    static function deletePopupMatch($pdo, $idMatch){
        require '../lang/fr.php';
        $req="DELETE FROM matchgame WHERE id_match=:id_match;";
        $pdo->prepare($req,[
            'id_match' => $idMatch
        ]);
        $pdo->alterAuto('matchgame');
        popup($title_deleted,"index.php?page=match");
    }
    
    static function createForm($pdo, $error, $form){
        require '../lang/fr.php';
        $val = $error->getError();
        $val .= "<form action='index.php?page=matchday' method='POST' onsubmit='return confirm();'>\n";
        $val .= $form->inputAction('create');
        $val .= $form->input($title_number,'number');
        $val .= $form->submit($title_create);
        $val .= "</form>\n";
        return $val;
    }
    
    static function createPopup($pdo, $matchdayNumber){
        require '../lang/fr.php';
        $pdo->alterAuto('matchday');
        $req = "INSERT INTO matchday
        VALUES(NULL,:id_season,:id_championship,:number);";
        $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId'],
            'id_championship' => $_SESSION['championshipId'],
            'number' => $matchdayNumber
        ]);
        popup($title_created,"index.php?page=matchday");
    }
    
    static function createPopupMatch($pdo, $team1, $team2, $result, $odds1, $oddsD, $odds2, $date){
        require '../lang/fr.php';
        $pdo->alterAuto('matchgame');
        $req="INSERT INTO matchgame
            VALUES(NULL,'".$_SESSION['matchdayId']."','".$team1."','".$team2."','".$result."','".$odds1."','".$oddsD."','".$odds2."','".$date."',0,0,0,0);";
        $pdo->exec($req);
        popup($title_created,"index.php?page=match&create=1");
    }
    
    static function modifyForm($pdo, $data, $matchdayId, $error, $form){
        require '../lang/fr.php';
        $req = "SELECT * FROM matchday WHERE id_matchday=:id_matchday;";
        $data = $pdo->prepare($req,[
            'id_matchday' => $matchdayId
        ],true);
        
        $val .= $error->getError();
        $val .= " <form action='index.php?page=matchday' method='POST' onsubmit='return confirm();'>\n";
        $form->setValues($data);
        $val .= $form->inputAction('modify');
        $val .= $form->inputHidden("id_matchday", $data->id_matchday);
        $val .= $form->input($title_number, "number");
        $val .= $form->submit($title_modify);
        $val .= " </form>\n";
        // Delete
        $val .= $form->deleteForm('matchday', 'id_matchday', $matchdayId);
        return $val;
    }
    
    static function modifyPopup($pdo, $matchdayNumber, $matchdayId){
        require '../lang/fr.php';
        $req="UPDATE matchday
        SET number=:number
        WHERE id_matchday=:id_matchday;";
        $pdo->prepare($req,[
            'number' => $matchdayNumber,
            'id_matchday' => $matchdayId
        ]);
        popup($title_modified,"index.php?page=matchday");
    }
    
    static function modifyPopupMatch($pdo, $team1, $team2, $result, $idMatch){
        require '../lang/fr.php';
        $req="UPDATE matchgame
            SET id_matchday = :id_matchday, team_1=:team_1, team_2 = :team_2, result = :result
            WHERE id_match = :id_match;";
        $pdo->prepare($req,[
            'id_matchday' => $_SESSION['matchdayId'],
            'team_1' => $team1,
            'team_2' => $team2,
            'result' => $result,
            'id_match' => $idMatch
        ]);
        popup($title_modifyAMatch,"index.php?page=match");
    }
    
    static function createMatchForm($pdo, $error, $form){
        require '../lang/fr.php';
        $val = $error->getError();
        $val .= "<form action='index.php?page=match' method='POST'>\n";
        $val .= $form->inputAction('create');
        $val .= $form->inputHidden('matchdayId', $_SESSION['matchdayId']);
        
        $val .= $form->inputDate($title_date, 'date', '');
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
        
        $val .= $form->labelBr($title_odds);
        $val .= $form->inputNumber('1', 'odds1', '0', '0.01');
        $val .= $form->inputNumber($title_draw, 'oddsD', '0', '0.01');
        $val .= $form->inputNumber('2', 'odds2', '0', '0.01');
        $val .= "<br />";
        
        $val .= $form->labelBr($title_result);
        $val .= $form->labelId('1', '1', 'right');
        $val .= $form->inputRadio('1', 'result', '1', false);
        $val .= $form->labelId('D', $title_draw, 'right');
        $val .= $form->inputRadio('D', 'result', 'D', false);
        $val .= $form->labelId('2', '2', 'right');
        $val .= $form->inputRadio('2', 'result', '2', false);
        $val .= "<br />";
        
        $val .= $form->submit($title_create);
        
        $val .= "</form>\n";   
        return $val;
    }
    
    static function modifyFormMatch($pdo, $error, $form, $idMatch){
        require '../lang/fr.php';
        $req="SELECT m.id_matchgame,c1.name as name1,c2.name as name2,c1.id_team as id1,c2.id_team as id2, m.result, m.date, m.odds1, m.oddsD, m.odds2
            FROM matchgame m LEFT JOIN team c1 ON m.team_1=c1.id_team LEFT JOIN team c2 ON m.team_2=c2.id_team
            WHERE m.id_matchgame = :id_matchgame;";
        $data = $pdo->prepare($req,[
            'id_matchgame' => $idMatch
        ]);
        $val = $error->getError();
        $val .= "<form action='index.php?page=match' method='POST'>\n";
        $form->setValues($data);
        $val .= $form->inputAction('modify');
        $val .= $form->inputHidden('id_matchgame',$data->id_matchgame);
        
        $val .= $form->inputDate($title_date, 'date', $data->date);
        $val .= "<br />";
        
        $val .= $form->selectTeam($pdo,'team_1',$data->id1);
        $val .= $form->selectTeam($pdo,'team_2',$data->id2);
        $val .= "<br />";
        
        $val .= $form->labelBr($title_odds);
        $val .= $form->inputNumber('1', 'odds1', $data->odds1, '0.01');
        $val .= $form->inputNumber($title_draw, 'oddsD', $data->oddsD, '0.01');
        $val .= $form->inputNumber('2', 'odds2', $data->odds2, '0.01');
        $val .= "<br />";
        
        $val .= $form->labelBr($title_result);
        $val .= $form->labelId('1', '1', 'right');
        if($data->result=="1") $val .= $form->inputRadio('1', 'result', '1', true);
        else $val .= $form->inputRadio('1', 'result', '1', false);
        
        $val .= $form->labelId('D', $title_draw, 'right');
        if($data->result=="D") $val .= $form->inputRadio('D', 'result', 'D', true);
        else $val .= $form->inputRadio('D', 'result', 'D', false);

        $val .= $form->labelId('2', '2', 'right');
        if($data->result=="2") $val .= $form->inputRadio('2', 'result', '2', true);
        else $val .= $form->inputRadio('2', 'result', '2', false);
        $val .= "<br />";
        
        $val .= $form->submit($title_modify);
        $val .= "</form>\n";
        
        // Delete
        $val .= $form->deleteForm('match', 'id_matchgame', $idMatch);
        
        return $val;
    }
    
    static function list($pdo){
        require '../lang/fr.php';
        changeMD($pdo,"matchday");
        echo "<h3>$title_statistics</h3>";
        $req="SELECT m.id_matchgame,
        cr.motivation1,cr.motivation2,
        cr.currentForm1,cr.currentForm2,
        cr.physicalForm1,cr.physicalForm2,
        cr.weather1,cr.weather2,
        cr.bestPlayers1,cr.bestPlayers2,
        cr.marketValue1,cr.marketValue2,
        cr.home_away1,cr.home_away2,
        c1.name as name1,c2.name as name2,c1.id_team as eq1,c2.id_team as eq2,
        m.result, m.date, m.odds1, m.oddsD, m.odds2 FROM matchgame m
        LEFT JOIN team c1 ON m.team_1=c1.id_team
        LEFT JOIN team c2 ON m.team_2=c2.id_team
        LEFT JOIN criterion cr ON cr.id_match=m.id_matchgame
        WHERE m.id_matchday=:id_matchday ORDER BY m.date
        ;";
        $data = $pdo->prepare($req,[
            'id_matchday' => $_SESSION['matchdayId']
        ],true);
        $counter = $pdo->rowCount();
        if($counter > 0){
            
            $table="	 <table class='stats'>\n";
            $table.="  		<tr>\n";
            $table.="  		  <th>$title_match</th>\n";
            $table.="         <th>$title_prediction</th>\n";
            $table.="         <th>$title_result</th>\n";
            $table.="         <th>$title_odds</th>\n";
            $table.="         <th>$title_success</th>\n";
            $table.="       </tr>\n";
            
            $matchs=$success=$earningSum=$totalJouee=0;
            
            foreach ($data as $d)
            {
                
                // Marketvalue
                $v1=criterion("v1",$d,$pdo);
                $v2=criterion("v2",$d,$pdo);
                $mv1 = round(sqrt($v1/$v2));
                $mv2 = round(sqrt($v2/$v1));
                
                $dom = $d->home_away1;
                $ext = $d->home_away2;
                
                // Predictions history
                $req="SELECT SUM(CASE WHEN m.result = '1' THEN 1 ELSE 0 END) AS Home,
                    SUM(CASE WHEN m.result = 'D' THEN 1 ELSE 0 END) AS Draw,
                    SUM(CASE WHEN m.result = '2' THEN 1 ELSE 0 END) AS Away
                    FROM matchgame m
                    LEFT JOIN criterion cr ON cr.id_match=m.id_matchgame
                    WHERE cr.motivation1='".$d->motivation1."'
                    AND cr.motivation2='".$d->motivation2."'
                    AND cr.currentForm1='".$d->currentForm1."'
                    AND cr.currentForm2='".$d->currentForm2."'
                    AND cr.physicalForm1='".$d->physicalForm1."'
                    AND cr.physicalForm2='".$d->physicalForm2."'
                    AND cr.weather1='".$d->weather1."'
                    AND cr.weather2='".$d->weather2."'
                    AND cr.bestPlayers1='".$d->bestPlayers1."'
                    AND cr.bestPlayers2='".$d->bestPlayers2."'
                    AND cr.marketValue1='".$d->marketValue1."'
                    AND cr.marketValue2='".$d->marketValue2."'
                    AND cr.home_away1='".$d->home_away1."'
                    AND cr.home_away2='".$d->home_away2."'
                    AND m.date<'".$d->date."'";
                $r = $pdo->prepare($req,'');
                $predictionsHistoryHome=criterion("predictionsHistoryHome",$r,$pdo);
                $predictionsHistoryAway=criterion("predictionsHistoryAway",$r,$pdo);
                
                // Sum
                $win="";
                
                $sum1=
                $d->motivation1
                +$d->currentForm1
                +$d->physicalForm1
                +$d->weather1
                +$d->bestPlayers1
                +$mv1
                +$dom
                +$predictionsHistoryHome;
                $sum2=
                $d->motivation2
                +$d->currentForm2
                +$d->physicalForm2
                +$d->weather2
                +$d->bestPlayers2
                +$mv2
                +$ext
                +$predictionsHistoryAway;
                if($sum1>$sum2) $prediction="1";
                elseif($sum1==$sum2) $prediction=$title_draw;
                elseif($sum1<$sum2) $prediction="2";
                
                $matchs++;
                
                $playedOdds=0;
                switch($prediction){
                    case "1":
                        $playedOdds = $d->odds1;
                        break;
                    case "N":
                        $playedOdds = $d->oddsD;
                        break;
                    case "2":
                        $playedOdds = $d->odds2;
                        break;
                }
                
                if($prediction==$d->result){
                    $win = $icon_winOK;
                    $success++;
                    $earningSum += $playedOdds;
                } elseif ($d->result!="") $win = $icon_winKO;
                $totalJouee+=$playedOdds;
                
                $table.="  		<tr>\n";
                $table.="  		  <td>".$d->name1." - ".$d->name2."</td>\n";
                $table.="  		  <td>".$prediction."</td>\n";
                $table.="  		  <td>";
                if($d->result=='D') $table.=$title_draw;
                else $table.=$d->result;
                $table.="</td>\n";
                $table.="  		  <td>".$playedOdds."</td>\n";
                $table.="  		  <td>".$win."</td>\n";
                $table.="       </tr>\n";
                
            }
            
            $table.="	 </table>\n";
            
            // Values
            $benef=money_format('%i',$earningSum-$matchs);
            $roi = round(($benef/$matchs)*100);
            $successRate = (($success/$matchs)*100);
            $earning = money_format('%i',$earningSum);
            $earningByBet = (round($earningSum/$matchs,2));
            
            echo "<p>\n";
            echo "  <table class='stats'>\n";
            
            echo "    <tr>\n";
            echo "      <td>$title_bet</td>\n";
            echo "      <td>".$matchs."</td>\n";
            echo "      <td>$title_profit</td>\n";
            echo "      <td><span style='color:".valColor($benef)."'>";
            if($benef>0) echo "+";
            echo $benef."</span></td>\n";
            echo "      <td>$title_ROI</td>\n";
            echo "      <td>";
            echo "<span style='color:".valColor($roi)."'>";
            if($roi>0) echo "+";
            echo $roi."&nbsp;%</span>";
            echo "&nbsp;<a href='#' class='tooltip'>&#128172;".valRoi($roi)."</a>";
            echo "</td>\n";
            echo "    </tr>\n";
            
            echo "    <tr>\n";
            echo "      <td>$title_success</td>\n";
            echo "      <td>$success</td>\n";
            echo "      <td>$title_earning</td>\n";
            echo "      <td>".$earning."&nbsp;&euro;</td>\n";
            echo "      <td>$title_earningByBet</td>\n";
            echo "      <td>$earningByBet</td>\n";
            echo "    </tr>\n";
            
            echo "    <tr>\n";
            echo "      <td>$title_successRate</td>\n";
            echo "      <td>";
            if($matchs>0) echo $successRate;
            else echo 0;
            echo "&nbsp;%</td>\n";
            $averageOdds=(round($totalJouee/$matchs,2));
            echo "      <td>$title_oddsAveragePlayed</td>\n";
            echo "      <td>".$averageOdds;
            if(($averageOdds<1.8)||($averageOdds>2.3)){
                echo "&nbsp;<a href='#' class='tooltip'>&#128172;".valOdds($averageOdds)."</a>";
            }
            echo "</td>\n";
            echo "      <td></td>\n";
            echo "      <td></td>\n";
            echo "    </tr>\n";
            
            echo "  </table>\n";
            echo "</p>\n";
            
            echo $table;
        } else echo $title_noStatistic;
    }
}
?>