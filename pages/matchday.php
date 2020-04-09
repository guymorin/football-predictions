<?php
/* This is the Football Predictions matchday section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\Errors;
use FootballPredictions\Forms;

// Files to include
require '../include/changeMD.php';
?>

<h2><?= "$icon_matchday $title_matchday ".(isset($_SESSION['matchdayNum']) ? $_SESSION['matchdayNum'] : null);?></h2>

<?php
// Values
$matchdayId=0;
$matchdayNumber="";

if(isset($_POST['matchdaySelect'])){
    $v=explode(",",$_POST['matchdaySelect']);
    $matchdayId=$v[0];
}

isset($_POST['id_matchday'])   ? $matchdayId=$error->check("Digit",$_POST['id_matchday']) : null;
isset($_POST['number'])		   ? $matchdayNumber=$error->check("Digit",$_POST['number']) : null;

$create=$modify=$delete=0;
isset($_GET['create'])		   ? $create=$error->check("Action",$_GET['create']) : null;
isset($_POST['create'])		   ? $create=$error->check("Action",$_POST['create']) : null;
isset($_POST['modify'])		   ? $modify=$error->check("Action",$_POST['modify']) : null;
isset($_POST['delete'])		   ? $delete=$error->check("Action",$_POST['delete']) : null;

$equipe=$idPlayer=$ratingPlayer=$deletePlayer=0;
isset($_POST['equipe'])		   ? $equipe=$error->check("Digit",$_POST['equipe']) : null;
isset($_POST['id_player'])	   ? $idPlayer=$error->check("Digit",$_POST['id_player']) : null;
isset($_POST['rating'])		   ? $ratingPlayer=$error->check("Digit",$_POST['rating']) : null;
isset($_POST['delete'])		   ? $deletePlayer=$error->check("Digit",$_POST['delete']) : null;

$val=array_combine($idPlayer,$ratingPlayer);

// Only if there is a matchday selected
if(isset($_SESSION['matchdayId'])){

    // Modify popup
    if($equipe==1){
        $pdo->alterAuto('teamOfTheWeek');
        $req="";
        foreach($deletePlayer as $d){
            $req="DELETE FROM teamOfTheWeek 
            WHERE id_matchday=:id_matchday  
            AND id_player=:id_player;";
            $pdo->prepare($req,[
                'id_matchday' => $_SESSION['matchdayId'],
                'id_player' => $d
            ]);
        }
        $pdo->alterAuto('teamOfTheWeek');
        $req="";
        foreach($val as $k=>$v){
            if(($v!="")&&(!in_array($k,$deletePlayer))){
                $req = "SELECT COUNT(*) as nb FROM teamOfTheWeek
                WHERE id_matchday=:id_matchday
                AND id_player='".$k."';";
                $data = $pdo->prepare($req,[
                    'id_matchday' => $_SESSION['matchdayId'],
                    'id_player' => $k
                ]);
                
                if($data->nb==0){
                    $req.="INSERT INTO teamOfTheWeek VALUES(NULL,:id_matchday,:id_player,:rating);";
                }
                if($data->nb==1){
                    $req.="UPDATE teamOfTheWeek SET rating=:rating WHERE id_matchday=:id_matchday AND id_player=:id_player;";
                }
                
                
            }
        } 
        $pdo->prepare($req,[
            'id_matchday' => $_SESSION['matchdayId'],
            'id_player' => $k,
            'rating' => $v
        ]);
        popup($title_modified,"index.php?matchday");
    }
    // Default page
    else {
    
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

// Delete popup
elseif($delete==1){
    $req="DELETE FROM matchday WHERE id_matchday='".$matchdayId."';";
    $pdo->exec($req);
    $pdo->alterAuto('matchday');
    popup($title_deleted,"index.php?page=matchday");
}
// Create
elseif($create==1){
    echo "<h3>$title_createAMatchday</h3>\n";
    // Create popup
    if($matchdayNumber!=""){
        $pdo->alterAuto('matchday');
        $req="INSERT INTO matchday 
        VALUES(NULL,:id_season,:id_championship,:number);";
        $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId'],
            'id_championship' => $_SESSION['championshipId'],
            'number' => $matchdayNumber
        ]);
        popup($title_created,"index.php?page=matchday");
    }
    // Create form
    else {
        echo $error->getError();
        echo "<form action='index.php?page=matchday' method='POST' onsubmit='return confirm();'>\n";
    	echo $form->inputAction('create'); 
    	echo $form->input($title_number,'number');
    	echo $form->submit($title_create);
    	echo "</form>\n";   
	}
}
// Modify
elseif($modify==1){
    echo "<h2>$title_modifyAMatchday</h2>\n";
    // Modify popup
    if($matchdayNumber!=""){
        $req="UPDATE matchday 
        SET number=:number  
        WHERE id_matchday=:id_matchday;";
        $pdo->prepare($req,[
            'number' => $matchdayNumber,
            'id_matchday' => $matchdayId
        ]);
        popup($title_modified,"index.php?page=matchday");
    }
    // Modify form
    else {
        $req = "SELECT * FROM matchday WHERE id_matchday=:id_matchday;";
        $data = $pdo->prepare($req,[
            'id_matchday' => $matchdayId
        ],true);

        echo $error->getError();
        echo " <form action='index.php?page=matchday' method='POST' onsubmit='return confirm();'>\n";
        $form->setValues($data);
        echo $form->inputAction('modify');    
        echo $form->inputHidden("id_matchday", $data->id_matchday);
        echo $form->input($title_number, "number");
        echo $form->submit($title_modify);
        echo " </form>\n";
        
        // Delete form
        echo "<form action='index.php?page=matchday' method='POST' onsubmit='return confirm()'>\n";
        echo $form->inputAction('delete');
        echo $form->inputHidden("id_matchday", $matchdayId);
        echo $form->inputHidden("number", "number");
        echo $form->submit("&#9888 $title_delete &#9888");
        echo "</form>\n";
        
    }
}
// Form select
else {
    echo "<form action='index.php?page=matchday' method='POST'>\n";
    require 'matchday_select.php';
    echo "</form>\n";
}
?>