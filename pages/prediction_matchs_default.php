<?php
// Predictions matchgame default include file

changeMD($db,"prediction"); // Arrows to change MD
echo "<h3>$title_prediction</h3>\n";

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
LEFT JOIN criterion cr ON cr.id_match=m.id_matchgame 
WHERE m.id_matchday=:id_matchday ORDER BY m.date;";
$response = $db->prepare($req);
$response->execute([
    'id_matchday' => $_SESSION['matchdayId']
]);

if($response->rowCount()>0){
    // Switch form
    echo "<form id='criterion' action='index.php?page=prediction' method='POST'>\n";
    echo "  <input type='hidden' name='expert' value='1'>\n";
    echo "  <input type='submit' value='$title_swithToExpert'>\n";
    echo "</form>\n";
    
    // Modify form
    echo "<form id='criterion' action='index.php?page=prediction' method='POST' onsubmit='return confirm();'>\n";
    echo "  <input type='hidden' name='modify' value='1'>\n";
    
    
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
    $r = $db->prepare($req);
    $r->execute([
        'id_season' => $_SESSION['seasonId'],
        'id_championship' => $_SESSION['championshipId']
    ]);
    while($data=$r->fetchColumn(0))   $domBonus[] = $data;
    
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
    $r = $db->prepare($req);
    $r->execute([
        'id_season' => $_SESSION['seasonId'],
        'id_championship' => $_SESSION['championshipId']
    ]);
    while($data=$r->fetchColumn(0))   $domMalus[] = $data;
    
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
    $r = $db->prepare($req);
    $r->execute([
        'id_season' => $_SESSION['seasonId'],
        'id_championship' => $_SESSION['championshipId']
    ]);
    
    while($data=$r->fetchColumn(0))   $extBonus[] = $data;
    
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
    $r = $db->prepare($req);
    $r->execute([
        'id_season' => $_SESSION['seasonId'],
        'id_championship' => $_SESSION['championshipId']
    ]);
    
    while($data=$r->fetchColumn(0))   $extMalus[] = $data;
    // Predictions for the matchday
    while ($data = $response->fetch(PDO::FETCH_OBJ))
    {
        
        // Motivation
        $motivC1=criterion("motivC1",$data,$db);
        $motivC2=criterion("motivC2",$data,$db);
        
        // Current form
        $serieC1=criterion("serieC1",$data,$db);
        $serieC2=criterion("serieC2",$data,$db);
        
        // Market value
        $v1=criterion("v1",$data,$db);
        $v2=criterion("v2",$data,$db);
        $mv1 = round(sqrt($v1/$v2));
        $mv2 = round(sqrt($v2/$v1));
        
        // Home / Away
        $dom = 0;
        if(in_array($data->eq1,$domBonus)) $dom=1;
        if(in_array($data->eq1,$domMalus)) $dom=(-1);
        $ext = 0;
        if(in_array($data->eq2,$extBonus)) $ext=1;
        if(in_array($data->eq2,$extMalus)) $ext=(-1);
        
        // Weather
        if($data->date!=""){
            
            $date1 = new DateTime($data->date);
            $date2 = new DateTime(date('Y-m-d'));
            $diff = $date2->diff($date1)->format("%a");
            $cloud="";
            
            if($diff>=0){
                $api="https://api.meteo-concept.com/api/forecast/daily/".$diff."?token=1aca29e38eb644104b41975b55a6842fc4fb2bfd2f79f85682baecb1c5291a3e&insee=".$data->weather_code;
                $weatherData = file_get_contents($api);
                $rain=0;
                
                if ($weatherData !== false){
                    $decoded = json_decode($data);
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
        
        if(($data->result!="")||(isset($data->weather1))) $team1Weather=$data->weather1;
        if(($data->result!="")||(isset($data->weather2))) $team2Weather=$data->weather2;
        
        
        // Predictions history
        $req="SELECT SUM(CASE WHEN m.result = '1' THEN 1 ELSE 0 END) AS Home,
    SUM(CASE WHEN m.result = 'D' THEN 1 ELSE 0 END) AS Draw,
    SUM(CASE WHEN m.result = '2' THEN 1 ELSE 0 END) AS Away
    FROM matchgame m
    LEFT JOIN criterion cr ON cr.id_match=m.id_matchgame
    WHERE cr.motivation1='".$data->motivation1."'
    AND cr.motivation2='".$data->motivation2."'
    AND cr.currentForm1='".$data->currentForm1."'
    AND cr.currentForm2='".$data->currentForm2."'
    AND cr.physicalForm1='".$data->physicalForm1."'
    AND cr.physicalForm2='".$data->physicalForm2."'
    AND cr.weather1='".$team1Weather."'
    AND cr.weather2='".$team2Weather."'
    AND cr.bestPlayers1='".$data->bestPlayers1."'
    AND cr.bestPlayers2='".$data->bestPlayers2."'
    AND cr.marketValue1='".$data->marketValue1."'
    AND cr.marketValue2='".$data->marketValue2."'
    AND cr.home_away1='".$data->home_away1."'
    AND cr.home_away2='".$data->home_away2."'
    AND m.date<'".$data->date."'";
        $r = $db->query($req)->fetch(PDO::FETCH_OBJ);
        $historyHome=criterion("predictionsHistoryHome",$r,$db);
        $historyDraw=criterion("msNul",$r,$db);
        $historyAway=criterion("predictionsHistoryAway",$r,$db);
        // Criterion sum
        $win="";
        $id=$data->id_matchgame;
        $sum1=
        $data->motivation1
        +$serieC1
        +$data->physicalForm1
        +$team1Weather
        +$data->bestPlayers1
        +$mv1
        +$dom
        +$historyHome;
        $sum2=
        $data->motivation2
        +$serieC2
        +$data->physicalForm2
        +$team2Weather
        +$data->bestPlayers2
        +$mv2
        +$ext
        +$historyAway;
        if($sum1>$sum2) $prediction="1";
        elseif($sum1==$sum2) $prediction="D";
        elseif($sum1<$sum2) $prediction="2";
        if(($historyDraw>$sum1)&&($historyDraw>$sum2)) $prediction="N";
        
        // Display table
        if($data->result=="") echo "<input type='hidden' name='id_match[]' value='".$data->id_matchgame."'>";
        echo $history[0];
        
        echo "	 <table>\n";
        
        echo "  		<tr>\n";
        echo "  		  <th>".$data->date."\n";
        echo "            <th>".$data->name1."</th>\n";
        echo "            <th></th>\n";
        echo "            <th>".$data->name2."</th>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>$title_motivation</td>\n";
        if($data->result!="") echo "<td>".$motivC1."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='motivation1[$id]' value='".$motivC1."' placeholder='0'></td>\n";
        echo "  		  <td></td>\n";
        if($data->result!="") echo "<td>".$motivC2."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='motivation2[$id]' value='".$motivC2."' placeholder='0'></td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>$title_currentForm</td>";
        if($data->result!="") echo "<td>".$serieC1."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='currentForm1[$id]' value='".$serieC1."' placeholder='0'></td>\n";
        echo "  		  <td></td>\n";
        if($data->result!="") echo "<td>".$serieC2."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='currentForm2[$id]' value='".$serieC2."' placeholder='0'></td>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>$title_physicalForm</td>\n";
        if($data->result!="") echo "<td>".$data->physicalForm1."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='physicalForm1[$id]' value='".$data->physicalForm1."' placeholder='0'></td>\n";
        echo "  		  <td></td>\n";
        if($data->result!="") echo "<td>".$data->physicalForm2."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='physicalForm2[$id]' value='".$data->physicalForm2."' placeholder='0'></td>\n";
        echo "          </tr>\n";
        
        
        echo "  		<tr>\n";
        echo "  		  <td>$title_weather <big>".$cloud."</big></td>\n";
        if($data->result!="") echo "<td>".$team1Weather."</td>\n";
        else {
            echo "  		  <td><input size='1' type='text' readonly name='meteo1[$id]' value='".$team1Weather."'></td>\n";
        }
        echo "  		  <td></td>\n";
        
        if($data->result!="") echo "<td>".$team2Weather."</td>\n";
        else {
            echo "  		  <td><input size='1' type='text' readonly name='meteo2[$id]' value='".$team2Weather."'></td>\n";
        }
        
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>$title_bestPlayers</td>\n";
        if($data->result!="") echo "<td>".$data->bestPlayers1."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='bestPlayers1[$id]' value='".$data->bestPlayers1."' placeholder='0'></td>\n";
        echo "  		  <td></td>\n";
        if($data->result!="") echo "<td>".$data->bestPlayers2."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='bestPlayers2[$id]' value='".$data->bestPlayers2."' placeholder='0'></td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>$title_marketValue</td>\n";
        if($data->result!="") echo "<td>".$data->marketValue1."</td>\n";
        else echo "  		  <td><input size='1' type='text' readonly name='marketValue1[$id]' value='".$mv1."'></td>\n";
        echo "  		  <td></td>\n";
        if($data->result!="") echo "<td>".$data->marketValue2."</td>\n";
        else echo "  		  <td><input size='1' type='text' readonly name='marketValue2[$id]' value='".$mv2."'></td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>$title_home / $title_away</td>";
        if($data->result!="") echo "<td>".$data->home_away1."</td>\n";
        else echo "  		  <td><input size='1' type='text' readonly name='home_away1[$id]' value='".$dom."'></td>\n";
        echo "  		  <td></td>\n";
        if($data->result!="") echo "<td>".$data->home_away2."</td>\n";
        else echo "  		  <td><input size='1' type='text' readonly name='home_away2[$id]' value='".$ext."'></td>\n";
        echo "          </tr>\n";
        
        echo "          <tr>\n";
        echo "            <td>$title_predictionsHistory</td>\n";
        echo "            <td>$historyHome</td>\n";
        echo "            <td>$historyDraw</td>\n";
        echo "            <td>$historyAway</td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td><strong>$title_criterionSum</strong></td>\n";
        echo "  		  <td><strong>$sum1</strong></td>\n";
        echo "  		  <td></td>\n";
        echo "  		  <td><strong>$sum2</strong></td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>$title_prediction</td>\n";
        echo "  		  <td><input type='radio' readonly id='1' value='1'";
        if($prediction=="1") echo " checked";
        echo "></td>\n";
        echo "  		  <td><input type='radio' readonly id='N' value='N'";
        if($prediction=="D") echo " checked";
        echo "></td>\n";
        echo "  		  <td><input type='radio' readonly id='2' value='2'";
        if($prediction=="2") echo " checked";
        echo "></td>\n";
        echo "          </tr>\n";
        
        if($data->result!=""){
            echo "  		<tr>\n";
            echo "  		  <td>$title_result</td>\n";
            echo "  		  <td><input type='radio'  readonly id='1' value='1'";
            if($data->result=="1") echo " checked";
            echo "></td>\n";
            echo "  		  <td><input type='radio'  readonly id='N' value='N'";
            if($data->result=="D") echo " checked";
            echo "></td>\n";
            echo "  		  <td><input type='radio'  readonly id='2' value='2'";
            if($data->result=="2") echo " checked";
            echo "></td>\n";
            echo "          </tr>\n";
        }
        echo "	 </table>\n";
    }
    echo $form->submit($title_modify);
    echo "</form>\n";
    $response->closeCursor();
    
} else echo $title_noMatch;
?>  
