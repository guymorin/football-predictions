<?php
/* This is the Football Predictions results section page */
/* Author : Guy Morin */

// Files to include
include("include/inc_changeMD.php");
include("matchday_nav.php");

echo "<section>\n";

$modify=0;
if(isset($_POST['modify'])) $modify=$_POST['modify'];
// Modify
// Modify popup
if($modify==1){
    if(isset($_POST['id_matchgame'])) $idMatch=$_POST['id_matchgame'];
    if(isset($_POST['result'])) $rMatch=$_POST['result'];
    if(isset($_POST['date'])) $dMatch=$_POST['date'];
    if(isset($_POST['odds1'])) $c1Match=$_POST['odds1'];
    if(isset($_POST['oddsD'])) $cNMatch=$_POST['oddsD'];
    if(isset($_POST['odds2'])) $c2Match=$_POST['odds2'];
    if(isset($_POST['red1'])) $r1Match=$_POST['red1'];
    if(isset($_POST['red2'])) $r2Match=$_POST['red2'];
    $cpt=0;
    $req="";
    foreach($idMatch as $k){
        $req.="UPDATE matchgame SET ";
        $req.="result='".$rMatch[$k]."'";
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
        $req.=" WHERE id_match='".$k."';";
    }
    
    $db->exec($req);
    popup($title_modified,"index.php?page=results");

}
// Modify form    
else {    
    changeMD($db,$title_results." ".$title_MD,"results");
    echo "<form id='results' action='index.php?page=results' method='POST' onsubmit='return confirm();'>\n";
    echo "      <input type='hidden' name='modify' value='1'>\n";
    $req="SELECT m.id_matchgame,
        c1.name as name1,c2.name as name2,
        m.result, m.date, m.odds1, m.oddsD, m.odds2, m.red1, m.red2 FROM matchgame m
        LEFT JOIN team c1 ON m.team_1=c1.id_team
        LEFT JOIN team c2 ON m.team_2=c2.id_team
        WHERE m.id_matchday='".$_SESSION['matchdayId']."' ORDER BY m.date;";
    $response = $db->query($req);
    
    echo "	 <table>\n";
    
    echo "  		<tr>\n";
    echo "            <th>$title_date</th>\n";
    echo "            <th>$title_notPlayed</th>\n";
    echo "  		  <th>$title_match</th>\n";
    echo "  		  <th>$title_odds</th>\n";
    echo "            <th>1</th>\n";
    echo "            <th>$title_draw</th>\n";
    echo "            <th>2</th>\n";
    echo "            <th colspan='2'>$title_redCards</th>\n";
    echo "          </tr>\n";
    
    while ($data = $response->fetch())
    {
        $id=$data['id_matchgame'];
        echo "<input type='hidden' name='id_match[]' value='".$data['id_matchgame']."'>\n";
        echo "  		<tr>\n";
        echo "  		  <td><input type='date' name='date[$id]' value='".$data['date']."'></td>";
        echo "  		  <td><input type='radio' name='result[$id]' value=''";
        if($data['result']=="") echo " checked";
        echo "></td>\n";
        echo "  		  <td>".$data['name1']." - ".$data['name2']."</td>";
        echo "<td>";
        echo "1<input type='number' step='0.01' name='odds1[$id]' size='2' value='".$data['odds1']."'>\n";
        echo "$title_draw<input type='number' step='0.01' name='oddsD[$id]' size='2' value='".$data['oddsD']."'>\n";
        echo "2<input type='number' step='0.01' name='odds2[$id]' size='2' value='".$data['odds2']."'>\n";
        echo "</td>\n";
        echo "  		  <td><input type='radio' id='1' name='result[$id]' value='1'";
        if($data['result']=="1") echo " checked";
        echo "></td>\n";
        echo "  		  <td><input type='radio' id='D' name='result[$id]' value='D'";
        if($data['result']=="D") echo " checked";
        echo "></td>\n";
        echo "  		  <td><input type='radio' id='2' name='result[$id]' value='2'";
        if($data['result']=="2") echo " checked";
        echo "></td>\n";
        echo "<td><input type='number' name='red1[$id]' value='".$data['red1']."'></td>\n";
        echo "<td><input type='number' name='red2[$id]' value='".$data['red2']."'></td>\n";
        echo "          </tr>\n";
        
    }
    $response->closeCursor();
    echo "	 </table>\n";
    
    echo "   <div><input type='submit'></div>\n";
    echo "</form>\n";
}
echo "</section>\n";
