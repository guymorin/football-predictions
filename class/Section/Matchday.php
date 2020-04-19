<?php 
/**
 * 
 * Class Matchday
 * Manage Matchday page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use \PDO;

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
        
        $val = "  	<a href='/'>" . (Language::title('homepage')) . "</a>";
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
            if($counter > 1){
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
        
        $req="DELETE FROM matchday WHERE id_matchday='".$matchdayId."';";
        $pdo->exec($req);
        $pdo->alterAuto('matchday');
        popup(Language::title('deleted'),"index.php?page=matchday");
    }
    
    static function deletePopupMatch($pdo, $idMatch){
        
        $req="DELETE FROM matchgame WHERE id_matchgame=:id_matchgame;";
        $pdo->prepare($req,[
            'id_matchgame' => $idMatch
        ]);
        $pdo->alterAuto('matchgame');
        popup(Language::title('deleted'),"index.php?page=matchgame");
    }
    
    static function createForm($pdo, $error, $form){
        
        $val = $error->getError();
        $val .= "<form action='index.php?page=matchday' method='POST'>\n";
        $val .= $form->inputAction('create');
        $val .= $form->input(Language::title('number'),'number');
        $val .= $form->submit(Language::title('create'));
        $val .= "</form>\n";
        return $val;
    }
    
    static function createMultiForm($pdo, $error, $form){
        
        $val = $error->getError();
        $val .= "<form action='index.php?page=matchday&create=1' method='POST'>\n";
        $val .= $form->inputAction('createMulti');
        $val .= $form->input(Language::title('matchdayNumber'),'totalNumber');
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
            VALUES(NULL,'".$_SESSION['matchdayId']."','".$team1."','".$team2."','".$result."','".$odds1."','".$oddsD."','".$odds2."','".$date."',0,0,0,0);";
        $pdo->exec($req);
        popup(Language::title('created'),"index.php?page=matchgame&create=1");
    }
    
    static function modifyForm($pdo, $data, $matchdayId, $error, $form){
        
        $req = "SELECT * FROM matchday WHERE id_matchday=:id_matchday;";
        $data = $pdo->prepare($req,[
            'id_matchday' => $matchdayId
        ]);
        
        $val .= $error->getError();
        $val .= " <form action='index.php?page=matchday' method='POST' onsubmit='return confirm();'>\n";
        $form->setValues($data);
        $val .= $form->inputAction('modify');
        $val .= $form->inputHidden("id_matchday", $data->id_matchday);
        $val .= $form->input(Language::title('number'), "number");
        $val .= "<br />\n";
        $val .= $form->submit(Language::title('modify'));
        $val .= " </form>\n";
        // Delete
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
    
    static function modifyPopupMatch($pdo, $team1, $team2, $result, $idMatch){
        
        $req="UPDATE matchgame
            SET id_matchday = :id_matchday, team_1=:team_1, team_2 = :team_2, result = :result
            WHERE id_matchgame = :id_matchgame;";
        $pdo->prepare($req,[
            'id_matchday' => $_SESSION['matchdayId'],
            'team_1' => $team1,
            'team_2' => $team2,
            'result' => $result,
            'id_matchgame' => $idMatch
        ]);
        popup(Language::title('modifyAMatch'),"index.php?page=matchgame");
    }
    
    static function createMatchForm($pdo, $error, $form){
        
        $val = $error->getError();
        $val .= "<form action='index.php?page=matchgame' method='POST'>\n";
        $val .= $form->inputAction('create');
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
        $val .= $form->inputRadioResult();
        $val .= "<br />";
        $val .= $form->submit(Language::title('create'));
        
        $val .= "</form>\n";   
        return $val;
    }
    
    static function modifyFormMatch($pdo, $error, $form, $idMatch){
        
        $req="SELECT m.id_matchgame,c1.name as name1,c2.name as name2,c1.id_team as id1,c2.id_team as id2, m.result, m.date, m.odds1, m.oddsD, m.odds2
            FROM matchgame m LEFT JOIN team c1 ON m.team_1=c1.id_team LEFT JOIN team c2 ON m.team_2=c2.id_team
            WHERE m.id_matchgame = :id_matchgame;";
        $data = $pdo->prepare($req,[
            'id_matchgame' => $idMatch
        ]);
        $val = $error->getError();
        $val .= "<form action='index.php?page=matchgame' method='POST'>\n";
        $form->setValues($data);
        $val .= $form->inputAction('modify');
        $val .= $form->inputHidden('id_matchgame',$data->id_matchgame);
        $val .= $form->inputDate(Language::title('date'), 'date', $data->date);
        $val .= "<br />";
        $val .= $form->selectTeam($pdo,'team_1',$data->id1);
        $val .= $form->selectTeam($pdo,'team_2',$data->id2);
        $val .= "<br />";
        $val .= $form->inputNumberOdds($data);
        $val .= $form->inputRadioResult($data);
        $val .= "<br />";
        $val .= $form->submit(Language::title('modify'));
        $val .= "</form>\n";
        // Delete
        $val .= $form->deleteForm('matchgame', 'id_matchgame', $idMatch);
        
        return $val;
    }
    
    static function stats($pdo){
        require '../theme/default/theme.php';
        changeMD($pdo,"statistics");
        echo "<h3>" . (Language::title('statistics')) . "</h3>";
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
        LEFT JOIN criterion cr ON cr.id_matchgame=m.id_matchgame
        WHERE m.id_matchday=:id_matchday ORDER BY m.date
        ;";
        $data = $pdo->prepare($req,[
            'id_matchday' => $_SESSION['matchdayId']
        ],true);
        $counter = $pdo->rowCount();
        if($counter > 0){
            
            $table="	 <table class='stats'>\n";
            $table.="  		<tr>\n";
            $table.="  		  <th>" . (Language::title('matchgame')) . "</th>\n";
            $table.="         <th>" . (Language::title('prediction')) . "</th>\n";
            $table.="         <th>" . (Language::title('result')) . "</th>\n";
            $table.="         <th>" . (Language::title('odds')) . "</th>\n";
            $table.="         <th>" . (Language::title('success')) . "</th>\n";
            $table.="       </tr>\n";
            
            $matchs = $success = $earningSum = $totalPlayed = 0;
            
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
                    LEFT JOIN criterion cr ON cr.id_matchgame=m.id_matchgame
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
                $r = $pdo->prepare($req,null,true);
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
                if($sum1>$sum2)         $prediction = '1';
                elseif($sum1==$sum2)    $prediction = Language::title('draw');
                elseif($sum1<$sum2)     $prediction = '2';
                
                $matchs++;
                $playedOdds=0;
                switch($prediction){
                    case "1":
                        $playedOdds = $d->odds1;
                        break;
                    case (Language::title('draw')):
                        $playedOdds = $d->oddsD;
                        break;
                    case "2":
                        $playedOdds = $d->odds2;
                        break;
                }
                if($prediction == $d->result){
                    $win = $icon_winOK;
                    $success++;
                    $earningSum += $playedOdds;
                } elseif ($d->result != "") $win = $icon_winKO;
                $totalPlayed += $playedOdds;
                
                $table.="  		<tr>\n";
                $table.="  		  <td>".$d->name1." - ".$d->name2."</td>\n";
                $table.="  		  <td>".$prediction."</td>\n";
                $table.="  		  <td>";
                if($d->result=='D') $table.=Language::title('draw');
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
            echo "      <td>" . (Language::title('bet')) . "</td>\n";
            echo "      <td>".$matchs."</td>\n";
            echo "      <td>" . (Language::title('success')) . "</td>\n";
            echo "      <td>" . $success . "</td>\n";
            echo "    </tr>\n";
            
            echo "    <tr>\n";
            echo "      <td>" . (Language::title('earning')) . "</td>\n";
            echo "      <td>".$earning."&nbsp;&euro;</td>\n";
            echo "      <td>" . (Language::title('successRate')) . "</td>\n";
            echo "      <td>";
            if($matchs>0) echo $successRate;
            else echo 0;
            echo "&nbsp;%</td>\n";
            echo "    </tr>\n";
            
            echo "    <tr>\n";
            echo "      <td>" . (Language::title('earningByBet')) . "</td>\n";
            echo "      <td>$earningByBet</td>\n";
            $averageOdds=(round($totalPlayed/$matchs,2));
            echo "      <td>" . (Language::title('oddsAveragePlayed')) . "</td>\n";
            echo "      <td>".$averageOdds;
            if(($averageOdds<1.8)||($averageOdds>2.3)){
                echo "&nbsp;<a href='#' class='tooltip'>&#128172;".valOdds($averageOdds)."</a>";
            }
            echo "</td>\n";
            echo "    </tr>\n";
            
            echo "    <tr>\n";
            echo "      <td>" . (Language::title('profit')) . "</td>\n";
            echo "      <td><span style='color:".valColor($benef)."'>";
            if($benef>0) echo "+";
            echo $benef."</span></td>\n";
            echo "      <td>" . (Language::title('ROI')) . "</td>\n";
            echo "      <td>";
            echo "<span style='color:".valColor($roi)."'>";
            if($roi>0) echo "+";
            echo $roi."&nbsp;%</span>";
            echo "&nbsp;<a href='#' class='tooltip'>&#128172;".valRoi($roi)."</a>";
            echo "</td>\n";
            echo "    </tr>\n";
            
            echo "  </table>\n";
            echo "</p>\n";
            
            echo $table;
        } else echo Language::title('noStatistic');
    }
    
    static function list($pdo, $form){
        
        $req = "SELECT md.id_matchday, md.number, COUNT(*) as nb, COUNT(mg.result) as played
        FROM matchday md
        LEFT JOIN matchgame mg ON mg.id_matchday=md.id_matchday
        WHERE md.id_season = :id_season 
        AND md.id_championship = :id_championship
        GROUP BY md.id_matchday, md.number
        ORDER BY md.number DESC";
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
                if($d->number==$_SESSION['matchdayNum']) {
                    $val .= "  <tr class='current'>\n";
                    $val .= "      <td>" . (Language::title('MD')) . ($d->number) . "</td>\n";
                }
                else {
                    $val .= "  <tr>\n";
                    $val .= "<form id='" . ($d->id_matchday) . "' action='index.php?page=matchday' method='POST'>\n";
                    $val .= $form->inputHidden("matchdaySelect", $d->id_matchday . "," . $d->number);
                    $val .= "<td>";
                    $val .= "<a href='#' onclick='document.getElementById(" . ($d->id_matchday) . ").submit();'>" . (Language::title('MD')) . ($d->number) . "</a>";
                    $val .= "</td>\n";
                }
                $val .= "      <td>" . $d->nb . "</td>\n";
                $val .= "      <td>" . $d->played . "</td>\n";
                $val .= "</form>\n";
                $val .= "  </tr>\n";
            }            
        } else {
            $val .= "  <tr>\n";
            $val .= "      <td>" . (Language::title('noMatchday')) . "</td>\n";
            $val .= "      <td>-</td>\n";
            $val .= "      <td>-</td>\n";
            $val .= "  </tr>\n";
        }

        $val .= "</table>\n";
        return $val;
    }
    
    
}
?>