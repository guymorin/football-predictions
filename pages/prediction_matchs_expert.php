<?php
// Predictions matchgame expert include file
echo "<h3>$title_prediction</h3>\n";

$req="SELECT m.id_matchgame,
cr.motivation1,cr.motivation2,
cr.currentForm1,cr.currentForm2,
cr.physicalForm1,cr.physicalForm2,
cr.weather1,cr.weather2,
cr.bestPlayers1,cr.bestPlayers2,
cr.marketValue1,cr.marketValue2,
cr.home_away1,cr.home_away2,
c1.name as name1,c2.name as name2,
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
    echo "  <input type='hidden' name='modify' value='2'>\n";
    echo "  <input id='edition 'type='hidden' name='expert' value='0'>\n";
    echo "  <input type='submit' value='$title_swithToDefault'>\n";
    echo "</form>\n";
    
    // Modify form
    echo "<form id='criterion' action='index.php?page=prediction' method='POST' onsubmit='return confirm();'>\n";
    echo "  <input type='hidden' name='modify' value='1'>\n";
    echo "  <input type='hidden' name='expert' value='1'>\n";
    
    
    // Predictions for the matchday
    while ($data = $response->fetch(PDO::FETCH_OBJ))
    {
        $win="";
        $id=$data->id_matchgame;
        $sum1=
            $data->motivation1
            +$data->currentForm1
            +$data->physicalForm1
            +$data->weather1
            +$data->bestPlayers1
            +$data->marketValue1
            +$data->home_away1;
        $sum2=
            $data->motivation2
            +$data->currentForm2
            +$data->physicalForm2
            +$data->weather2
            +$data->bestPlayers2
            +$data->marketValue2
            +$data->home_away2;
        if($sum1>$sum2) $prediction="1";
        elseif($sum1==$sum2) $prediction="N";
        elseif($sum1<$sum2) $prediction="2";
        if($prediction==$data->result) $win=" ";
            
        echo "	 <table class='expert'>\n";
       
        echo "  		<tr>\n";
        echo "  		  <th></th>\n";
        echo "            <th>".$data->name1."</th>\n";
        echo "            <th><input type='hidden' name='id_match[]' value='".$data->id_matchgame."'></th>\n";
        echo "            <th>".$data->name2."</th>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>$title_motivation</td>";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='motivation1[$id]' value='".$data->motivation1."'></td>\n";
        echo "  		  <td></td>\n";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='motivation2[$id]' value='".$data->motivation2."'></td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>$title_currentForm</td>";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='currentForm1[$id]' value='".$data->currentForm1."'></td>\n";
        echo "  		  <td></td>\n";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='currentForm2[$id]' value='".$data->currentForm2."'></td>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>$title_physicalForm</td>";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='physicalForm1[$id]' value='".$data->physicalForm1."'></td>\n";
        echo "  		  <td></td>\n";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='physicalForm2[$id]' value='".$data->physicalForm2."'></td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>$title_weather</td>";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='meteo1[$id]' value='".$data->weather1."'></td>\n";
        echo "  		  <td></td>\n";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='meteo2[$id]' value='".$data->weather2."'></td>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>$title_bestPlayers</td>";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='bestPlayers1[$id]' value='".$data->bestPlayers1."'></td>\n";
        echo "  		  <td></td>\n";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='bestPlayers2[$id]' value='".$data->bestPlayers2."'></td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>$title_marketValue</td>";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='marketValue1[$id]' value='".$data->marketValue1."'></td>\n";
        echo "  		  <td></td>\n";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='marketValue2[$id]' value='".$data->marketValue2."'></td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>$title_home / $title_away</td>";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='home_away1[$id]' value='".$data->home_away1."'></td>\n";
        echo "  		  <td></td>\n";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='home_away2[$id]' value='".$data->home_away2."'></td>\n";
        echo "          </tr>\n";
    
        echo "  		<tr>\n";
        echo "  		  <td>$title_criterionSum</td>";
        echo "  		  <td>$sum1</td>\n";
        echo "  		  <td></td>\n";
        echo "  		  <td>$sum2</td>\n";
        echo "          </tr>\n";
    
        echo "  		<tr>\n";
        echo "  		  <td>$title_prediction".$win."</td>";
        echo "  		  <td><input type='radio' readonly id='1' value='1'";
        if($prediction=="1") echo " checked";
        echo "></td>\n";
        echo "  		  <td><input type='radio' readonly id='N' value='N'";
        if($prediction=="N") echo " checked";
        echo "></td>\n";
        echo "  		  <td><input type='radio' readonly id='2' value='2'";
        if($prediction=="2") echo " checked";
        echo "></td>\n";
        echo "          </tr>\n";
        
        
        echo "  		<tr>\n";
        echo "  		  <td>$title_result</td>";
        echo "  		  <td><input name='result[$id]' readonly type='radio' id='1' value='1'";
        if($data->result=="1") echo " checked";
        echo "></td>\n";
        echo "  		  <td><input name='result[$id]' readonly type='radio' id='N' value='N'";
        if($data->result=="N") echo " checked";
        echo "></td>\n";
        echo "  		  <td><input name='result[$id]' readonly type='radio' id='2' value='2'";
        if($data->result=="2") echo " checked";
        echo "></td>\n";
        echo "          </tr>\n";
        
        echo "	 </table>\n";
    }
    $response->closeCursor();
    
    echo "      <div><input type='submit'></div>\n";
    echo "	 </form>\n";
} else echo $title_noMatch;
?>  
