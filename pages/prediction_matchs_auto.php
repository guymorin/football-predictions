<?php
// Predictions matchgame default include file

use FootballPredictions\Language;
use FootballPredictions\Theme;

echo "<h3>" . (Language::title('prediction')) . "</h3>\n";

$prediction = $team1Weather = $team2Weather = $cloud = $history = "";

// Select data
$data = result('selectCriterion',$pdo);
$counter = $pdo->rowCount();

if($counter > 0){
    
    if($_SESSION['role']==2){
        // Switch form
        echo "<form id='criterion' action='index.php?page=prediction' method='POST'>\n";
        echo $form->inputHidden('modify','2');
        echo $form->inputHidden('manual','1');
        echo $form->submit(Language::title('swithToManual'));
        echo "</form>\n";
        echo "<br />\n";
    }

    // Modify form
    echo "<form id='criterion' action='index.php?page=prediction' method='POST' onsubmit='return confirm();'>\n";
    echo $form->inputAction('modify');
    
    /* Requests */
    // Best teams home
    $r = result('bestHome',$pdo);
    foreach($r as $v) $domBonus[] = $v->id_team;
    
    // Worst teams home
    $r = result('worstHome',$pdo);
    foreach($r as $v) $domMalus[] = $v->id_team;
    
    // Best teams away
    $r = result('bestAway',$pdo);
    foreach($r as $v) $extBonus[] = $v->id_team;
    
    // Worst teams away
    $r = result('worstAway',$pdo);
    foreach($r as $v) $extMalus[] = $v->id_team;

    // Predictions for the matchday
    foreach ($data as $d)
    {
        if($d->result==""){
        
            // Motivation
            $motivC1=criterion("motivC1",$d,$pdo);
            $motivC2=criterion("motivC2",$d,$pdo);
            
            // Current form
            $serieC1=criterion("serieC1",$d,$pdo);
            $serieC2=criterion("serieC2",$d,$pdo);

            // Physical form
            $physicalC1=criterion("physicalC1",$d,$pdo);
            $physicalC2=criterion("physicalC2",$d,$pdo);
            
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
                        $decoded = json_decode($weatherData);
                        $city = $decoded->city;
                        $forecast = $decoded->forecast;
                        $rain=$forecast->rr1;
                    }
                    switch($rain){
                        case ($rain==0):
                            $cloud="&#x1F323;";// Sun
                        case ($rain>=0&&$rain<1):
                            $cloud="&#x1F324;";// Low rain
                            $weather=1;
                            if(round($v2/10)>round($v1/10)) $team2Weather=$weather;
                            elseif(round($v2/10)==round($v1/10)){
                                $team1Weather=$weather;
                                $team2Weather=$weather;
                            }
                            else {
                                $team1Weather=$weather;
                                $team2Weather=0;
                            }
                            break;
                        case ($rain>=1&&$rain<3):
                            $cloud="&#x1F326;";// Middle rain
                            $weather=1;
                            if(round($v2/10)>round($v1/10)) $team1Weather=$weather;
                            elseif(round($v2/10)==round($v1/10)){
                                $team1Weather=$weather;
                                $team2Weather=$weather;
                            }
                            else {
                                $team1Weather=0;
                                $team2Weather=$weather;
                            }
                            break;
                        case ($rain>=3):
                            $cloud="&#x1F327;";//High rain
                            $weather=2;
                            if(round($v2/10)>round($v1/10)) $team1Weather=$weather;
                            elseif(round($v2/10)==round($v1/10)){
                                $team1Weather=$weather;
                                $team2Weather=$weather;
                            }
                            else {
                                $team1Weather=0;
                                $team2Weather=$weather;
                            }
                            break;
                    }
                    
                }
            }
        // Else = there is a result
        } else {
            $motivC1 = $d->motivation1;
            $motivC2 = $d->motivation2;
            $serieC1 = $d->currentForm1;
            $serieC2 = $d->currentForm2;
            $physicalC1 = $d->physicalForm1;
            $physicalC2 = $d->physicalForm1;
            $team1Weather = $d->weather1;
            $team2Weather = $d->weather2;
            $mv1 = $d->marketValue1;
            $mv2 = $d->marketValue2;
            $dom = $d->home_away1;
            $ext = $d->home_away2;
        }
        
        // Predictions history
        $historyHome=$historyDraw=$historyAway=0;
        $r = result('history',$pdo,$d,$team1Weather,$team2Weather);
        $historyHome=criterion("predictionsHistoryHome",$r,$pdo);
        $historyDraw=criterion("predictionsHistoryDraw",$r,$pdo);
        $historyAway=criterion("predictionsHistoryAway",$r,$pdo);
        
// Criterion sum
        $win = "";
        $id = $d->id_matchgame;
        
        $sum1 = 
            $motivC1
            +$serieC1
            +$physicalC1
            +intval($team1Weather)
            +$d->bestPlayers1
            +$mv1
            +$dom
            +$historyHome;
        $sum2 = 
            $motivC2
            +$serieC2
            +$physicalC2
            +intval($team2Weather)
            +$d->bestPlayers2
            +$mv2
            +$ext
            +$historyAway;
       
        $sumD = setSumD($sum1,$sum2,$historyDraw);
        
        $prediction = setPrediction($sum1, $sumD, $sum2);
       
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
        else echo "  		  <td><input size='1' type='text' name='currentForm1[$id]' readonly value='".$serieC1."'></td>\n";
        echo "  		  <td></td>\n";
        if($d->result!="") echo "<td>".$serieC2."</td>\n";
        else echo "  		  <td><input size='1' type='text' name='currentForm2[$id]' readonly value='".$serieC2."'></td>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>" . (Language::title('physicalForm')) . "</td>\n";
        if($d->result!="") echo "<td>".$physicalC1."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='physicalForm1[$id]' value='".$physicalC1."' placeholder='0'></td>\n";
        echo "  		  <td></td>\n";
        if($d->result!="") echo "<td>".$physicalC2."</td>\n";
        else echo "  		  <td><input size='1' type='number' name='physicalForm2[$id]' value='".$physicalC2."' placeholder='0'></td>\n";
        echo "          </tr>\n";
        
        
        echo "  		<tr>\n";
        echo "  		  <td>" . (Language::title('weather')) . " <big>".$cloud."</big></td>\n";
        if($d->result!="") echo "<td>".$team1Weather."</td>\n";
        else {
            echo "  		  <td><input size='1' type='text' readonly name='weather1[$id]' value='".$team1Weather."'></td>\n";
        }
        echo "  		  <td></td>\n";
        
        if($d->result!="") echo "<td>".$team2Weather."</td>\n";
        else {
            echo "  		  <td><input size='1' type='text' readonly name='weather2[$id]' value='".$team2Weather."'></td>\n";
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
        if($d->result!="") echo "<td>".$mv1."</td>\n";
        else echo "  		  <td><input size='1' type='text' readonly name='marketValue1[$id]' value='".$mv1."'></td>\n";
        echo "  		  <td></td>\n";
        if($d->result!="") echo "<td>".$mv2."</td>\n";
        else echo "  		  <td><input size='1' type='text' readonly name='marketValue2[$id]' value='".$mv2."'></td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>" . (Language::title('home')) . " / " . (Language::title('away')) . "</td>";
        if($d->result!="") echo "<td>".$dom."</td>\n";
        else echo "  		  <td><input size='1' type='text' readonly name='home_away1[$id]' value='".$dom."'></td>\n";
        echo "  		  <td></td>\n";
        if($d->result!="") echo "<td>".$ext."</td>\n";
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
        echo "  		  <td><strong>$sumD</strong></td>\n";
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
            if($d->result == '1'){
                if($d->result == $prediction) echo Theme::icon('winOK');
                else echo Theme::icon('OK');
            } else echo Theme::icon('KO');
            echo "</td>\n";
            echo "  		  <td>";
            if($d->result == 'D'){
                if($d->result == $prediction) echo Theme::icon('winOK');
                else echo Theme::icon('OK');
            } else echo Theme::icon('KO');
            echo "</td>\n";
            echo "  		  <td>";
            if($d->result == '2'){
                if($d->result == $prediction) echo Theme::icon('winOK');
                else echo Theme::icon('OK');
            } else echo Theme::icon('KO');
            echo "</td>\n";
            echo "          </tr>\n";
        }
        echo "	 </table>\n";
    }
    echo $form->submit(Language::title('modify'));
    echo "</form>\n";
    
} else echo Language::title('noMatch');
?>  
