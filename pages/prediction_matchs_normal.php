<?php
// Predictions matchgame default include file

use FootballPredictions\Language;
use FootballPredictions\Theme;

echo "<h3>" . (Language::title('prediction')) . "</h3>\n";

$prediction = $team1Weather = $team2Weather = $cloud = $history = "";

$req="SELECT m.id_matchgame,
cr.motivation1,cr.motivation2,
cr.currentForm1,cr.currentForm2,
cr.physicalForm1,cr.physicalForm2,
cr.weather1,cr.weather2,
cr.bestPlayers1,cr.bestPlayers2,
cr.marketValue1,cr.marketValue2,
cr.home_away1,cr.home_away2,
c1.name as name1,c2.name as name2,c1.id_team as eq1,c2.id_team as eq2,
c1.weather_code,
m.result, m.date FROM matchgame m 
LEFT JOIN team c1 ON m.team_1=c1.id_team 
LEFT JOIN team c2 ON m.team_2=c2.id_team 
LEFT JOIN criterion cr ON cr.id_matchgame=m.id_matchgame 
WHERE m.id_matchday=:id_matchday ORDER BY m.date;";
$data = $pdo->prepare($req,[
    'id_matchday' => $_SESSION['matchdayId']
],true);
$counter = $pdo->rowCount();

if($counter > 0){
    
    if($_SESSION['role']==2){
        // Switch form
        echo "<form id='criterion' action='index.php?page=prediction' method='POST'>\n";
        echo $form->inputHidden('modify','2');
        echo $form->inputHidden('expert','0');
        echo $form->submit(Language::title('swithToExpert'));
        echo "</form>\n";
        echo "<br />\n";
    }

    // Modify form
    echo "<form id='criterion' action='index.php?page=prediction' method='POST' onsubmit='return confirm();'>\n";
    echo $form->inputAction('modify');
    
    /* Requests */
    // Best teams home
    $req="
    SELECT c.id_team, c.name, COUNT(m.id_matchgame) as matchs,
    SUM(
        CASE WHEN m.result = '1' AND m.team_1=c.id_team THEN 3 ELSE 0 END +
        CASE WHEN m.result = 'D' AND m.team_1=c.id_team THEN 1 ELSE 0 END
    ) as points
    FROM team c
    LEFT JOIN season_championship_team scc ON c.id_team=scc.id_team
    LEFT JOIN matchday j ON (scc.id_season=j.id_season AND scc.id_championship=j.id_championship)
    LEFT JOIN matchgame m ON m.id_matchday=j.id_matchday
    WHERE scc.id_season=:id_season
    AND scc.id_championship=:id_championship
    AND (c.id_team=m.team_1 OR c.id_team=m.team_2)
    AND m.result<>''
    GROUP BY c.id_team,c.name
    ORDER BY points DESC
    LIMIT 0,5";
    $r = $pdo->prepare($req,[
        'id_season' => $_SESSION['seasonId'],
        'id_championship' => $_SESSION['championshipId']
    ],true);
    
    foreach($r as $v) $domBonus[] = $v->id_team;
    
    // Worst teams home
    $req="
    SELECT c.id_team, c.name, COUNT(m.id_matchgame) as matchs,
    SUM(
        CASE WHEN m.result = '1' AND m.team_1=c.id_team THEN 3 ELSE 0 END +
        CASE WHEN m.result = 'D' AND m.team_1=c.id_team THEN 1 ELSE 0 END
    ) as points
    FROM team c
    LEFT JOIN season_championship_team scc ON c.id_team=scc.id_team
    LEFT JOIN matchday j ON (scc.id_season=j.id_season AND scc.id_championship=j.id_championship)
    LEFT JOIN matchgame m ON m.id_matchday=j.id_matchday
    WHERE scc.id_season=:id_season 
    AND scc.id_championship=:id_championship 
    AND (c.id_team=m.team_1 OR c.id_team=m.team_2)
    AND m.result<>''
    GROUP BY c.id_team,c.name
    ORDER BY points ASC
    LIMIT 0,5";
    $r = $pdo->prepare($req,[
        'id_season' => $_SESSION['seasonId'],
        'id_championship' => $_SESSION['championshipId']
    ],true);
    
    foreach($r as $v) $domMalus[] = $v->id_team;
    
    // Best teams away
    $req="
    SELECT c.id_team, c.name, COUNT(m.id_matchgame) as matchs,
    SUM(
        CASE WHEN m.result = '1' AND m.team_2=c.id_team THEN 3 ELSE 0 END +
        CASE WHEN m.result = 'D' AND m.team_2=c.id_team THEN 1 ELSE 0 END
    ) as points
    FROM team c
    LEFT JOIN season_championship_team scc ON c.id_team=scc.id_team
    LEFT JOIN matchday j ON (scc.id_season=j.id_season AND scc.id_championship=j.id_championship)
    LEFT JOIN matchgame m ON m.id_matchday=j.id_matchday
    WHERE scc.id_season=:id_season
    AND scc.id_championship=:id_championship
    AND (c.id_team=m.team_1 OR c.id_team=m.team_2)
    AND m.result<>''
    GROUP BY c.id_team,c.name
    ORDER BY points ASC
    LIMIT 0,5";
    $r = $pdo->prepare($req,[
        'id_season' => $_SESSION['seasonId'],
        'id_championship' => $_SESSION['championshipId']
    ],true);
    
    foreach($r as $v) $extBonus[] = $v->id_team;
    
    // Worst teams away
    $req="
    SELECT c.id_team, c.name, COUNT(m.id_matchgame) as matchs,
    SUM(
        CASE WHEN m.result = '1' AND m.team_2=c.id_team THEN 3 ELSE 0 END +
        CASE WHEN m.result = 'D' AND m.team_2=c.id_team THEN 1 ELSE 0 END
    ) as points
    FROM team c
    LEFT JOIN season_championship_team scc ON c.id_team=scc.id_team
    LEFT JOIN matchday j ON (scc.id_season=j.id_season AND scc.id_championship=j.id_championship)
    LEFT JOIN matchgame m ON m.id_matchday=j.id_matchday
    WHERE scc.id_season=:id_season
    AND scc.id_championship=:id_championship
    AND (c.id_team=m.team_1 OR c.id_team=m.team_2)
    AND m.result<>''
    GROUP BY c.id_team,c.name
    ORDER BY points DESC
    LIMIT 0,5";
    $r = $pdo->prepare($req,[
        'id_season' => $_SESSION['seasonId'],
        'id_championship' => $_SESSION['championshipId']
    ],true);
    
    foreach($r as $v) $extMalus[] = $v->id_team;

    // Predictions for the matchday
    foreach ($data as $d)
    {
        // Motivation
        $motivC1=criterion("motivC1",$d,$pdo);
        $motivC2=criterion("motivC2",$d,$pdo);
        
        // Current form
        $serieC1=criterion("serieC1",$d,$pdo);
        $serieC2=criterion("serieC2",$d,$pdo);
        
        // Market value
        $v1=criterion("v1",$d,$pdo);
        $v2=criterion("v2",$d,$pdo);
        if( ($v1 != 0) && ($v2 != 0) ){
            $mv1 = round(sqrt($v1/$v2));
            $mv2 = round(sqrt($v2/$v1));
        } else {
            $mv1 = $mv2 = 0;
        }
        
        // Home / Away
        $dom = 0;
        if(is_array($domBonus)){
            if(in_array($d->eq1,$domBonus)) $dom=1;
        }
        if(is_array($domMalus)){
            if(in_array($d->eq1,$domMalus)) $dom=(-1);
        }
        $ext = 0;
        if(is_array($extBonus)){
            if(in_array($d->eq2,$extBonus)) $ext=1;
        }
        if(is_array($extMalus)){
            if(in_array($d->eq2,$extMalus)) $ext=(-1);
        }
        // Weather
        if($d->date!=""){
            
            $date1 = new DateTime($d->date);
            $date2 = new DateTime(date('Y-m-d'));
            $diff = $date2->diff($date1)->format("%a");
            $cloud="";
            
            if($diff>=0 && $diff<14){
                $api="https://api.meteo-concept.com/api/forecast/daily/".$diff."?token=1aca29e38eb644104b41975b55a6842fc4fb2bfd2f79f85682baecb1c5291a3e&insee=".$d->weather_code;
                $weatherData = file_get_contents($api);
                $rain=0;
                
                if ($weatherData !== false){
                    $decoded = json_decode($d);
                    $city = $decoded->city;
                    $forecast = $decoded->forecast;
                    $rain=$forecast->rr1;
                }
                switch($rain){
                    case ($rain==0):
                        $cloud="&#x1F323;";// Sun
                    case ($rain>=0&&$rain<1):
                        $cloud="&#x1F324;";// Low rain
                        $meteo=1;
                        if(round($v2/10)>round($v1/10)) $team2Weather=$meteo;
                        elseif(round($v2/10)==round($v1/10)){
                            $team1Weather=$meteo;
                            $team2Weather=$meteo;
                        }
                        else {
                            $team1Weather=$meteo;
                            $team2Weather=0;
                        }
                        break;
                    case ($rain>=1&&$rain<3):
                        $cloud="&#x1F326;";// Middle rain
                        $meteo=1;
                        if(round($v2/10)>round($v1/10)) $team1Weather=$meteo;
                        elseif(round($v2/10)==round($v1/10)){
                            $team1Weather=$meteo;
                            $team2Weather=$meteo;
                        }
                        else {
                            $team1Weather=0;
                            $team2Weather=$meteo;
                        }
                        break;
                    case ($rain>=3):
                        $cloud="&#x1F327;";//High rain
                        $meteo=2;
                        if(round($v2/10)>round($v1/10)) $team1Weather=$meteo;
                        elseif(round($v2/10)==round($v1/10)){
                            $team1Weather=$meteo;
                            $team2Weather=$meteo;
                        }
                        else {
                            $team1Weather=0;
                            $team2Weather=$meteo;
                        }
                        break;
                }
                
            }
        }
        
        if(($d->result!="")||(isset($d->weather1))) $team1Weather=$d->weather1;
        if(($d->result!="")||(isset($d->weather2))) $team2Weather=$d->weather2;
        
        
        // Predictions history
        $req="SELECT SUM(CASE WHEN m.result = '1' THEN 1 ELSE 0 END) AS Home,
        SUM(CASE WHEN m.result = 'D' THEN 1 ELSE 0 END) AS Draw,
        SUM(CASE WHEN m.result = '2' THEN 1 ELSE 0 END) AS Away
        FROM matchgame m
        LEFT JOIN criterion cr ON cr.id_matchgame=m.id_matchgame
        WHERE cr.motivation1 = :motivation1
        AND cr.motivation2 = :motivation2
        AND cr.currentForm1 = :currentForm1
        AND cr.currentForm2 = :currentForm2
        AND cr.physicalForm1 = :physicalForm1
        AND cr.physicalForm2 = :physicalForm2
        AND cr.weather1 = :weather1
        AND cr.weather2 = :weather2
        AND cr.bestPlayers1 = :bestPlayers1
        AND cr.bestPlayers2 = :bestPlayers2
        AND cr.marketValue1 = :marketValue1
        AND cr.marketValue2 = :marketValue2
        AND cr.home_away1 = :home_away1
        AND cr.home_away2 = :home_away2
        AND m.date < :mdate;";
        $r = $pdo->prepare($req,[
            'motivation1' => $d->motivation1,
            'motivation2' => $d->motivation2,
            'currentForm1' => $d->currentForm1,
            'currentForm2' => $d->currentForm2,
            'physicalForm1' => $d->physicalForm1,
            'physicalForm2' => $d->physicalForm2,
            'weather1' => $team1Weather,
            'weather2' => $team2Weather,
            'bestPlayers1' => $d->bestPlayers1,
            'bestPlayers2' => $d->bestPlayers2,
            'marketValue1' => $d->marketValue1,
            'marketValue2' => $d->marketValue2,
            'home_away1' => $d->home_away1,
            'home_away2' => $d->home_away2,
            'mdate' => $d->date
        ]);
        
        $historyHome=criterion("predictionsHistoryHome",$r,$pdo);
        $historyDraw=criterion("msNul",$r,$pdo);
        $historyAway=criterion("predictionsHistoryAway",$r,$pdo);
        
// Criterion sum
        $win = "";
        $id = $d->id_matchgame;
        
        $sum1 = 
            $d->motivation1
            +$serieC1
            +$d->physicalForm1
            +intval($team1Weather)
            +$d->bestPlayers1
            +$mv1
            +$dom
            +$historyHome;
        $sum2 = 
            $d->motivation2
            +$serieC2
            +$d->physicalForm2
            +intval($team2Weather)
            +$d->bestPlayers2
            +$mv2
            +$ext
            +$historyAway;
        if($sum1>$sum2)      $prediction = "1";
        elseif($sum1==$sum2) $prediction = "D";
        elseif($sum1<$sum2)  $prediction = "2";
        if(($historyDraw>$sum1)&&($historyDraw>$sum2)) $prediction="D";
        
        // Display table
        if($d->result=="") echo $form->inputHidden('id_match[]',$d->id_matchgame);
        if(isset($history[0])) echo $history[0];
        
        echo "	 <table>\n";
        
        echo "  		<tr>\n";
        echo "  		  <th>".$d->date."\n";
        echo "            <th>".$d->name1."</th>\n";
        echo "            <th></th>\n";
        echo "            <th>".$d->name2."</th>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>" . (Language::title('motivation')) . "</td>\n";
        if($d->result!="") echo "<td>".$motivC1."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='motivation1[$id]' value='".$motivC1."' placeholder='0'></td>\n";
        echo "  		  <td></td>\n";
        if($d->result!="") echo "<td>".$motivC2."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='motivation2[$id]' value='".$motivC2."' placeholder='0'></td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>" . (Language::title('currentForm')) . "</td>";
        if($d->result!="") echo "<td>".$serieC1."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='currentForm1[$id]' value='".$serieC1."' placeholder='0'></td>\n";
        echo "  		  <td></td>\n";
        if($d->result!="") echo "<td>".$serieC2."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='currentForm2[$id]' value='".$serieC2."' placeholder='0'></td>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>" . (Language::title('physicalForm')) . "</td>\n";
        if($d->result!="") echo "<td>".$d->physicalForm1."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='physicalForm1[$id]' value='".$d->physicalForm1."' placeholder='0'></td>\n";
        echo "  		  <td></td>\n";
        if($d->result!="") echo "<td>".$d->physicalForm2."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='physicalForm2[$id]' value='".$d->physicalForm2."' placeholder='0'></td>\n";
        echo "          </tr>\n";
        
        
        echo "  		<tr>\n";
        echo "  		  <td>" . (Language::title('weather')) . " <big>".$cloud."</big></td>\n";
        if($d->result!="") echo "<td>".$team1Weather."</td>\n";
        else {
            echo "  		  <td><input size='1' type='text' readonly name='meteo1[$id]' value='".$team1Weather."'></td>\n";
        }
        echo "  		  <td></td>\n";
        
        if($d->result!="") echo "<td>".$team2Weather."</td>\n";
        else {
            echo "  		  <td><input size='1' type='text' readonly name='meteo2[$id]' value='".$team2Weather."'></td>\n";
        }
        
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>" . (Language::title('bestPlayers')) . "</td>\n";
        if($d->result!="") echo "<td>".$d->bestPlayers1."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='bestPlayers1[$id]' value='".$d->bestPlayers1."' placeholder='0'></td>\n";
        echo "  		  <td></td>\n";
        if($d->result!="") echo "<td>".$d->bestPlayers2."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='bestPlayers2[$id]' value='".$d->bestPlayers2."' placeholder='0'></td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>" . (Language::title('marketValue')) . "</td>\n";
        if($d->result!="") echo "<td>".$d->marketValue1."</td>\n";
        else echo "  		  <td><input size='1' type='text' readonly name='marketValue1[$id]' value='".$mv1."'></td>\n";
        echo "  		  <td></td>\n";
        if($d->result!="") echo "<td>".$d->marketValue2."</td>\n";
        else echo "  		  <td><input size='1' type='text' readonly name='marketValue2[$id]' value='".$mv2."'></td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>" . (Language::title('home')) . " / " . (Language::title('away')) . "</td>";
        if($d->result!="") echo "<td>".$d->home_away1."</td>\n";
        else echo "  		  <td><input size='1' type='text' readonly name='home_away1[$id]' value='".$dom."'></td>\n";
        echo "  		  <td></td>\n";
        if($d->result!="") echo "<td>".$d->home_away2."</td>\n";
        else echo "  		  <td><input size='1' type='text' readonly name='home_away2[$id]' value='".$ext."'></td>\n";
        echo "          </tr>\n";
        
        echo "          <tr>\n";
        echo "            <td>" . (Language::title('predictionsHistory')) . "</td>\n";
        echo "            <td>$historyHome</td>\n";
        echo "            <td>$historyDraw</td>\n";
        echo "            <td>$historyAway</td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td><strong>" . (Language::title('criterionSum')) . "</strong></td>\n";
        echo "  		  <td><strong>$sum1</strong></td>\n";
        echo "  		  <td></td>\n";
        echo "  		  <td><strong>$sum2</strong></td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>" . (Language::title('prediction')) . "</td>\n";
        echo "  		  <td>";
        if($prediction == '1') echo Theme::icon('OK');
        else echo Theme::icon('KO');
        echo "</td>\n";
        echo "  		  <td>";
        if($prediction == 'D') echo Theme::icon('OK');
        else echo Theme::icon('KO');
        echo "</td>\n";
        echo "  		  <td>";
        if($prediction == '2') echo Theme::icon('OK');
        else echo Theme::icon('KO');
        echo "</td>\n";
        echo "          </tr>\n";

        if($d->result!=""){
            echo "  		<tr>\n";
            echo "  		  <td>" . (Language::title('result')) . "</td>\n";
            echo "  		  <td>";
            $varName = "result[$id]";
            if($d->result == '1') echo Theme::icon('OK');
            else echo Theme::icon('KO');
            echo "</td>\n";
            echo "  		  <td>";
            if($d->result == 'D') echo Theme::icon('OK');
            else echo Theme::icon('KO');
            echo "</td>\n";
            echo "  		  <td>";
            if($d->result == '2') echo Theme::icon('OK');
            else echo Theme::icon('KO');
            echo "</td>\n";
            echo "          </tr>\n";
        }
        echo "	 </table>\n";
    }
    echo $form->submit(Language::title('modify'));
    echo "</form>\n";
    
} else echo Language::title('noMatch');
?>  
