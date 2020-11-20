<?php
// Predictions matchgame manual include file
use FootballPredictions\Language;
use FootballPredictions\Theme;

echo "<h3>" . (Language::title('prediction')) . "</h3>\n";

// Select data
$data = result('selectCriterion',$pdo);
$counter = $pdo->rowCount();

if($counter > 0){
    
    if($_SESSION['role']==2){
        // Switch form
        echo "<form id='criterion' action='index.php?page=prediction' method='POST'>\n";
        echo $form->inputHidden('manual','0');
        echo $form->submit(Language::title('swithToAuto'));
        echo "</form>\n";
        echo "<br />\n";
    }
    // Modify form
    echo "<form id='criterion' action='index.php?page=prediction' method='POST' onsubmit='return confirm();'>\n";
    echo $form->inputAction('modify');
    echo $form->inputHidden('manual','1');

    // Predictions for the matchday
    foreach ($data as $d)
    {
        // Predictions history
        $historyHome=$historyDraw=$historyAway=0;
        $r = result('history',$pdo, $d, $d->weather1, $d->weather2);
        $historyHome=criterion("predictionsHistoryHome",$r,$pdo);
        $historyDraw=criterion("predictionsHistoryDraw",$r,$pdo);
        $historyAway=criterion("predictionsHistoryAway",$r,$pdo);

        // Trend
        $trend1= $trend2 = 0;
        if($_SESSION['matchdayNum']>3) {
            $trendTeam1 = criterion('trendTeam1', $d, $pdo);
            $trendTeam2 = criterion('trendTeam2', $d, $pdo);
            if($trendTeam1>4 and $trendTeam2<2){
                $trend1 = 1;
                $trend2 = -1;
            }
        }
        
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
            +$historyHome
            +$trend1;
        $sum2=
            $d->motivation2
            +$d->currentForm2
            +$d->physicalForm2
            +$d->weather2
            +$d->bestPlayers2
            +$d->marketValue2
            +$d->home_away2
            +$historyAway
            +$trend2;
        $sumD=setSumD($sum1, $sum2, $historyDraw);
        
        $prediction = setPrediction($sum1, $sumD, $sum2);
        if($prediction==$d->result) $win=" ";
            
        echo "	 <table class='prediction manual'>\n";
        echo "  		<tr>\n";
        echo "  		  <th>".Theme::icon('matchday')." ".Language::title('MD').$_SESSION['matchdayNum'];
        echo ", ".$d->date."<br />";
        echo Theme::icon('team')."&nbsp;".$d->name1."<br />";
        echo Theme::icon('team')."&nbsp;".$d->name2;
        echo "</th>\n";
        echo "            <th>1</th>\n";
        echo "            <th>";
        
        echo Language::title('draw');
        echo "<input type='hidden' name='id_match[]' value='".$d->id_matchgame."'>";
        echo "</th>\n";
        echo "            <th>2</th>\n";
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
        echo "  		  <td><input size='1' type='number' placeholder='0' name='weather1[$id]' value='".$d->weather1."'></td>\n";
        echo "  		  <td></td>\n";
        echo "  		  <td><input size='1' type='number' placeholder='0' name='weather2[$id]' value='".$d->weather2."'></td>\n";
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
        
        echo "          <tr>\n";
        echo "            <td>" . (Language::title('trend')) . "</td>\n";
        echo "            <td>$trend1</td>\n";
        echo "            <td>0</td>\n";
        echo "            <td>$trend2</td>\n";
        echo "          </tr>\n";
        
        echo "  		<tr>\n";
        echo "  		  <td><strong>" . (Language::title('criterionSum')) . "</strong></td>";
        echo "  		  <td><strong>$sum1</strong></td>\n";
        echo "  		  <td><strong>$sumD</strong></td>\n";
        echo "  		  <td><strong>$sum2</strong></td>\n";
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
        
        echo "	 </table>\n";
    
    }
    
    echo $form->submit(Language::title('modify'));
    
    echo "</form>\n";
    
} else echo Language::title('noMatch');
?>