<?php
/* This is the Football Predictions results section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\Errors;
use FootballPredictions\Forms;
use FootballPredictions\Language;
use FootballPredictions\Theme;

// Files to include
require 'include/changeMD.php';

echo "<h2>" . Theme::icon('matchday') . " " . (Language::title('matchday')) . " " . (isset($_SESSION['matchdayNum']) ? $_SESSION['matchdayNum'] : null)."</h2>\n";

// Modify
// Modify popup
if($modify==1){
    isset($_POST['id_matchgame'])   ? $idMatch=$error->check("Digit",$_POST['id_matchgame']) : null;
    isset($_POST['result'])         ? $rMatch=$error->check("Digit",$_POST['result'], Language::title('result')) : null;
    isset($_POST['date'])           ? $dMatch=$error->check("Date",$_POST['date'], Language::title('date')) : null;
    isset($_POST['odds1'])          ? $c1Match=$error->check("Digit",$_POST['odds1'], Language::title('odds').' 1') : null;
    isset($_POST['oddsD'])          ? $cNMatch=$error->check("Digit",$_POST['oddsD'], Language::title('odds').' '.Language::title('draw')) : null;
    isset($_POST['odds2'])          ? $c2Match=$error->check("Digit",$_POST['odds2'], Language::title('odds').' 2') : null;
    isset($_POST['red1'])           ? $r1Match=$error->check("Digit",$_POST['red1'], Language::title('redCards').' 1') : null;
    isset($_POST['red2'])           ? $r2Match=$error->check("Digit",$_POST['red2'], Language::title('redCards').' 2') : null;
    $cpt=0;
    $req="";
    foreach($idMatch as $k){
        $req.= "UPDATE matchgame SET ";
        $req.= "result='".$rMatch[$k]."'";
        $cpt=1;
        if($dMatch[$k]!=""){
            if($cpt==1){
                $req.=",";
                $cpt=0;
            }
            $req.="date='".$dMatch[$k]."'";
            $cpt=1;
        }
        if($c1Match[$k]>0){
            if($cpt==1){
                $req.=",";
                $cpt=0;
            }
            $req.="odds1='".$c1Match[$k]."'";
            $cpt=1;
        }
        if($cNMatch[$k]>0){
            if($cpt==1){
                $req.=",";
                $cpt=0;
            }
            $req.="oddsD='".$cNMatch[$k]."'";
            $cpt=1;
        }
        if($c2Match[$k]>0){
            if($cpt==1){
                $req.=",";
                $cpt=0;
            }
            $req.="odds2='".$c2Match[$k]."'";
            $cpt=1;
        }
        if($r1Match[$k]>=1){
            if($cpt==1){
                $req.=",";
                $cpt=0;
            }
            $req.="red1='".$r1Match[$k]."'";
            $cpt=1;
        }
        if($r2Match[$k]>=1){
            if($cpt==1){
                $req.=",";
                $cpt=0;
            }
            $req.="red2='".$r2Match[$k]."'";
            $cpt=1;
        }
        $req.=" WHERE id_matchgame='".$k."';";
    }
    $pdo->exec($req);
    popup(Language::title('modified'),"index.php?page=results");

}
// Modify form    
else {
    changeMD($pdo,"results");
    echo "<h3>" . (Language::title('results')) . "</h3>\n";

    $req="SELECT m.id_matchgame,
        c1.name as name1,c2.name as name2,
        m.result, m.date, m.odds1, m.oddsD, m.odds2, m.red1, m.red2 FROM matchgame m
        LEFT JOIN team c1 ON m.team_1=c1.id_team
        LEFT JOIN team c2 ON m.team_2=c2.id_team
        WHERE m.id_matchday=:id_matchday
        ORDER BY m.date;";
    $data = $pdo->prepare($req,[
        'id_matchday' => $_SESSION['matchdayId']
    ],true);
    $counter=$pdo->rowCount();
    if($counter > 0){
        echo "<form id='results' action='index.php?page=results' method='POST' onsubmit='return confirm();'>\n";
        echo $error->getError();
        echo $form->inputAction('modify');
        
        
        echo "<table>\n";
        
        echo "  <tr>\n";
        echo "      <th>" . (Language::title('date')) . "</th>\n";
        echo "      <th>" . (Language::title('notPlayed')) . "</th>\n";
        echo "      <th>" . (Language::title('matchgame')) . "</th>\n";
        echo "      <th>1</th>\n";
        echo "      <th>" . (Language::title('draw')) . "</th>\n";
        echo "      <th>2</th>\n";
        echo "      <th colspan='2'>" . (Language::title('redCards')) . "</th>\n";
        echo "  </tr>\n";
        
        foreach ($data as $d)
        {
            $form->setValues($d);
            $id=$d->id_matchgame;
            echo $form->inputHidden('id_match[]', $d->id_matchgame);
            echo "  <tr>\n";
            echo "      <td>".$form->inputDate("", "date[$id]", $d->date)."</td>\n";
            
            echo "  	<td>";
            if($d->result=="") echo $form->inputRadio("", "result[$id]", "", true);
            else  echo $form->inputRadio("", "result[$id]", "", false);
            echo "</td>\n";
            
            echo "  	<td>" . $d->name1." - " . $d->name2."</td>\n";
            
            echo "  	<td>";
            if($d->result=="1") echo $form->inputRadio("1", "result[$id]", "1", true);
            else echo $form->inputRadio("1", "result[$id]", "1", false);
            echo "<br />" . $form->labelBr(Language::title('odds'));
            echo $form->inputNumber("", "odds1[$id]",$d->odds1, '0.01');
            echo "</td>\n";
            
            echo "  	<td>";
            if($d->result=="D") echo $form->inputRadio("D", "result[$id]", "D", true);
            else echo $form->inputRadio("D", "result[$id]", "D", false);
            echo "<br />" . $form->labelBr(Language::title('odds'));
            echo $form->inputNumber("", "oddsD[$id]",$d->oddsD, '0.01');
            echo "</td>\n";
            
            echo "  	<td>";
            if($d->result=="2") echo $form->inputRadio("2", "result[$id]", "2", true);
            else echo $form->inputRadio("2", "result[$id]", "2", false);
            echo "<br />" . $form->labelBr(Language::title('odds'));
            echo $form->inputNumber("", "odds2[$id]",$d->odds2, '0.01');
            echo "</td>\n";
            
            echo "<td>".$form->inputNumber("", "red1[$id]",$d->red1, "")."</td>\n";
            
            echo "<td>".$form->inputNumber("", "red2[$id]",$d->red2, "")."</td>\n";
            
            echo "  </tr>\n";
        }
        
        echo "</table>\n";
        echo $form->submit(Language::title('modify'));
        echo "</form>\n";
    } else echo Language::title('noResult');
}
