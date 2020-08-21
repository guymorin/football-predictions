<?php
// Predictions matchgame manual include file
use FootballPredictions\Language;
use FootballPredictions\Theme;

echo "<h3>" . (Language::title('prediction')) . "</h3>\n";

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
        echo $form->inputHidden('manual','1');
        echo $form->submit(Language::title('swithToAuto'));
        echo "</form>\n";
        echo "<br />\n";
    }
    // Modify form
    echo "<form id='criterion' action='index.php?page=prediction' method='POST' onsubmit='return confirm();'>\n";
    echo $form->inputAction('modify');
    echo $form->inputHidden('manual','1');
    
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
    
    
    // Predictions for the matchday
    foreach ($data as $d)
    {
        $win="";
        $id=$d->id_matchgame;
        $sum1=
            $d->motivation1
            +$d->currentForm1
            +$d->physicalForm1
            +$d->weather1
            +$d->bestPlayers1
            +$d->marketValue1
            +$d->home_away1
            +$historyHome;
        $sum2=
            $d->motivation2
            +$d->currentForm2
            +$d->physicalForm2
            +$d->weather2
            +$d->bestPlayers2
            +$d->marketValue2
            +$d->home_away2
            +$historyAway;
        if($sum1>$sum2) $prediction="1";
        elseif($sum1==$sum2) $prediction="D";
        elseif($sum1<$sum2) $prediction="2";
        if(($historyDraw>$sum1)&&($historyDraw>$sum2)) $prediction="D";
        if($prediction==$d->result) $win=" ";
            
        echo "	 <table class='manual'>\n";
       
        echo "  		<tr>\n";
        echo "  		  <th>".$d->date."</th>\n";
        echo "            <th>".$d->name1."</th>\n";
        echo "            <th><input type='hidden' name='id_match[]' value='".$d->id_matchgame."'></th>\n";
        echo "            <th>".$d->name2."</th>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>" . (Language::title('motivation')) . "</td>";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='motivation1[$id]' value='".$d->motivation1."'></td>\n";
        echo "  		  <td></td>\n";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='motivation2[$id]' value='".$d->motivation2."'></td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>" . (Language::title('currentForm')) . "</td>";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='currentForm1[$id]' value='".$d->currentForm1."'></td>\n";
        echo "  		  <td></td>\n";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='currentForm2[$id]' value='".$d->currentForm2."'></td>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>" . (Language::title('physicalForm')) . "</td>";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='physicalForm1[$id]' value='".$d->physicalForm1."'></td>\n";
        echo "  		  <td></td>\n";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='physicalForm2[$id]' value='".$d->physicalForm2."'></td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>" . (Language::title('weather')) . "</td>";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='meteo1[$id]' value='".$d->weather1."'></td>\n";
        echo "  		  <td></td>\n";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='meteo2[$id]' value='".$d->weather2."'></td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>" . (Language::title('bestPlayers')) . "</td>";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='bestPlayers1[$id]' value='".$d->bestPlayers1."'></td>\n";
        echo "  		  <td></td>\n";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='bestPlayers2[$id]' value='".$d->bestPlayers2."'></td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>" . (Language::title('marketValue')) . "</td>";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='marketValue1[$id]' value='".$d->marketValue1."'></td>\n";
        echo "  		  <td></td>\n";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='marketValue2[$id]' value='".$d->marketValue2."'></td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td>" . (Language::title('home')) . " / " . (Language::title('away')) . "</td>";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='home_away1[$id]' value='".$d->home_away1."'></td>\n";
        echo "  		  <td></td>\n";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='home_away2[$id]' value='".$d->home_away2."'></td>\n";
        echo "          </tr>\n";
    
        
        echo "          <tr>\n";
        echo "            <td>" . (Language::title('predictionsHistory')) . "</td>\n";
        echo "            <td>$historyHome</td>\n";
        echo "            <td>$historyDraw</td>\n";
        echo "            <td>$historyAway</td>\n";
        echo "          </tr>\n";
        
        
        echo "  		<tr>\n";
        echo "  		  <td>" . (Language::title('criterionSum')) . "</td>";
        echo "  		  <td>$sum1</td>\n";
        echo "  		  <td></td>\n";
        echo "  		  <td>$sum2</td>\n";
        echo "          </tr>\n";
    
        echo "  		<tr>\n";
        echo "  		  <td>" . (Language::title('prediction')) . "</td>";
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
        
        echo "  		<tr>\n";
        echo "  		  <td>" . (Language::title('result')) . "</td>";
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
        
        echo "	 </table>\n";
    
    }
    
    echo $form->submit(Language::title('modify'));
    
    echo "</form>\n";
    
} else echo Language::title('noMatch');
?>