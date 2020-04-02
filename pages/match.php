<?php
/* This is the Football Predictions match section page */
/* Author : Guy Morin */

// Files to include
require("matchday_nav.php");

echo "<section>\n";

// Values
$error = new Errors();
$form = new Forms($_POST);

// No matchday selected
if(empty($_SESSION['matchdayId'])){

    // Popup matchday
    if(isset($_POST['matchdaySelect'])){
        $v=explode(",",$_POST['matchdaySelect']);
        $_SESSION['matchdayId']=$error->check("Digit",$v[0]);
        $_SESSION['matchdayNum']=$error->check("Digit",$v[1]);
        popup($title_MD.$_SESSION['matchdayNum'],"index.php?page=match");
    }
}
// Matchday selected
else {
    echo "<h2>$icon_matchday $title_matchday ".$_SESSION['matchdayNum']."</h2>\n";

    // Values
    $date=$result="";
    $idMatch=$team1=$team2=$odds1=$oddsD=$odds2=0;
    if(isset($_POST['id_matchgame'])) $idMatch=$error->check("Digit",$_POST['id_matchgame']);
    if(isset($_POST['team_1'])) $team1=$error->check("Digit",$_POST['team_1']);
    if(isset($_POST['team_2'])) $team2=$error->check("Digit",$_POST['team_2']);
    if(isset($_POST['result'])) $result=$error->check("Result",$_POST['result']);
    if(isset($_POST['odds1'])) $odds1=$error->check("Digit",$_POST['odds1']);
    if(isset($_POST['oddsD'])) $oddsD=$error->check("Digit",$_POST['oddsD']);
    if(isset($_POST['odds2'])) $odds2=$error->check("Digit",$_POST['odds2']);
    if(isset($_POST['date'])) $date=$error->check("Digit",$_POST['date']);
    $create=$modify=$delete=0;
    if(isset($_GET['create'])) $create=$error->check("Action",$_GET['create']);
    elseif(isset($_POST['create'])) $create=$error->check("Action",$_POST['create']);
    if(isset($_GET['modify'])) $modify=$error->check("Action",$_GET['modify']);
    elseif(isset($_POST['modify'])) $modify=$error->check("Action",$_POST['modify']);
    if(isset($_POST['delete'])) $delete=$error->check("Action",$_POST['delete']);

// Popup if needed
    // Delete
    if($delete==1){
            $req="DELETE FROM matchgame WHERE id_match='".$idMatch."';";
            $db->exec($req);
            $db->exec("ALTER TABLE matchgame AUTO_INCREMENT=0;");
            popup($title_deleted,"index.php?page=match");
    }
    // Create
    elseif($create==1){

        echo "<h3>$title_createAMatch</h3>\n";
        // Create popup
        if(($team1>0)&&($team2>0)&&($team1!=$team2)){
            $db->exec("ALTER TABLE matchgame AUTO_INCREMENT=0;");
            $req="INSERT INTO matchgame VALUES(NULL,'".$_SESSION['matchdayId']."','".$team1."','".$team2."','".$result."','".$odds1."','".$oddsD."','".$odds2."','".$date."',0,0,0,0);";
            $db->exec($req);
            popup($title_created,"index.php?page=match&create=1");
        }
        // Create form 
        else {
            echo "	  <form action='index.php?page=match' method='POST'>\n";
            echo $error->getError();
            echo "      <input type='hidden' name='create' value='1'>\n"; 
            echo "      <input type='hidden' readonly name='matchdayId' value='".$_SESSION['matchdayId']."'></p>\n"; 
            
            $req="SELECT c.id_team, c.name FROM team c LEFT JOIN season_championship_team scc ON c.id_team=scc.id_team WHERE scc.id_season='".$_SESSION['seasonId']."' AND scc.id_championship='".$_SESSION['championshipId']."' ORDER BY c.name;";
            $response = $db->query($req);
            
            echo "	    <label>$title_team 1</label>\n";
            echo "  	<select name='team_1'>\n";
            require("team_select.php");
            echo "  	</select>\n";
            echo "	    <label>$title_team 2</label>\n";
            $response = $db->query($req);
            echo "  	<select name='team_2'>\n";
            require("team_select.php");
            echo "  	</select>\n";
            
            $response->closeCursor();
            
            echo "	    <p><label>$title_date :</label>\n";
            echo "         <input type='date' name='date' value=''>\n";
            echo "      </p>\n";
            echo "	    <p><label>$title_odds :</label>\n";
            echo "         1<input type='number' step='0.01' size='2' name='odds1' value='0'>\n";
            echo "         $title_draw<input type='number' step='0.01' size='2' name='oddsD' value='0'>\n";
            echo "         2<input type='number' step='0.01' size='2' name='odds2' value='0'>\n";
            echo "      </p>\n";
            
            echo "	    <p><label>$title_result :</label>\n";
            echo "      <input type='radio' name='result' id='1' value='1'";
            echo "><label for='1'>1</label>\n";
            echo "      <input type='radio' name='result' id='D' value='D'";
            echo "><label for='D'>$title_draw</label>\n";
            echo "      <input type='radio' name='result' id='2' value='2'";
            echo "><label for='2'>2</label>\n";
            
            echo "      <input type='submit' value='$title_create'>\n";
    
            echo "	  </form>\n";   
    	}
    }
    // Modify
    else {
        echo "<h3>$title_modifyAMatch</h3>\n";
        // Modify popup
        if(($team1>0)&&($team2>0)&&($team1!=$team2)){
            $req="UPDATE matchgame SET id_matchday='".$_SESSION['matchdayId']."', team_1='".$team1."', team_2='".$team2."', result='".$result."' WHERE id_match='".$idMatch."';";
            $db->exec($req);
            popup($title_modifyAMatch,"index.php?page=match");
        } 
        // Modify form
        elseif($idMatch>0){
            $req="SELECT m.id_matchgame,c1.name as name1,c2.name as name2,c1.id_team as id1,c2.id_team as id2, m.result, m.date, m.odds1, m.oddsD, m.odds2 FROM matchgame m LEFT JOIN team c1 ON m.team_1=c1.id_team LEFT JOIN team c2 ON m.team_2=c2.id_team WHERE m.id_matchgame='".$idMatch."';";
            $response = $db->query($req);
            $data = $response->fetch(PDO::FETCH_OBJ);
            $name1=$data->name1;
            $name2=$data->name2;
            $id1=$data->id1;
            $id2=$data->id2;
            $result=$data->result;
            $date=$data->date;
            $odds1=$data->odds1;
            $oddsD=$data->oddsD;
            $odds2=$data->odds2;
            
            echo "	 <form action='index.php?page=match' method='POST'>\n";
            echo $error->getError();
            echo $form->inputAction("modify");    
            echo "      <input type='hidden' name='id_matchgame' readonly value='".$data->id_matchgame."'></p>\n";
            echo "	    <label>$title_team 1</label>\n";
            echo "  	<select name='team_1'>\n";
            echo "  		<option value='0'>...</option>\n";
            $response = $db->query("SELECT c.* FROM team c LEFT JOIN season_championship_team scc ON c.id_team=scc.id_team WHERE scc.id_season='".$_SESSION['seasonId']."' AND scc.id_championship='".$_SESSION['championshipId']."' ORDER BY name;");
            while ($data = $response->fetch(PDO::FETCH_OBJ))
            {
                echo "  		<option value='".$data->id_team."'";
                if($data->id_team==$id1) echo " selected";
                echo ">".$data->name."</option>\n";
            }
            echo "  	</select>\n";
            
            echo "	    <label>$title_team 2</label>\n";
            echo "  	<select name='team_2'>\n";
            $response = $db->query("SELECT c.* FROM team c LEFT JOIN season_championship_team scc ON c.id_team=scc.id_team WHERE scc.id_season='".$_SESSION['seasonId']."' AND scc.id_championship='".$_SESSION['championshipId']."' ORDER BY name;");      
            echo "  		<option value='0'>...</option>\n";
            while ($data = $response->fetch(PDO::FETCH_OBJ))
            {
                 echo "  		<option value='".$data->id_team."'";
                if($data->id_team==$id2) echo " selected";
                echo ">".$data->name."</option>\n";        }
            echo "  	</select>\n";
    
            echo "	    <p><label>$title_date :</label>\n";
            echo "         <input type='date' name='date' value='".$date."'>\n";
            echo "      </p>\n";
            echo "	    <p><label>$title_odds :</label>\n";
            echo "         1<input type='number' step='0.01' size='2' name='odds1' value='".$odds1."'>\n";
            echo "         $title_draw<input type='number' step='0.01' size='2' name='oddsD' value='".$oddsD."'>\n";
            echo "         2<input type='number' step='0.01' size='2' name='odds2' value='".$odds2."'>\n";
            echo "      </p>\n";
            
            echo "	    <p><label>$title_result :</label>\n";
            echo "     <input type='radio' name='result' id='1' value='1'";
            if($result=="1") echo " checked";
            echo "><label for='1'>1</label>\n";
            echo "     <input type='radio' name='result' id='D' value='D'";
            if($result=="D") echo " checked";
            echo "><label for='D'>$title_draw</label>\n";
            echo "     <input type='radio' name='result' id='2' value='2'";
            if($result=="2") echo " checked";
            echo "><label for='2'>2</label>\n";
            
            echo "      <input type='submit' value='$title_modify'>\n";
            echo "	 </form>\n";
            
            echo "	 <form action='index.php?page=match' method='POST' onsubmit='return confirm()'>\n";
           echo $form->inputAction("delete");
            echo "      <input type='hidden' name='id_matchgame' value=$idMatch>\n";
            echo "      <input type='submit' value='&#9888 $title_delete $name1 - $name2 &#9888'>\n";
            echo "	 </form>\n";
            $response->closeCursor();  
        }
        // Modify selection of a match
        else {

            echo "  <form action='index.php?page=match' method='POST'>\n";             // Modifier
            echo $error->getError();
            echo "      <input type='hidden' name='modify' value='1'>\n"; 
            echo "      <label>$title_modifyAMatch :</label>\n";                                    
            echo "  	<select multiple size='10' name='id_matchgame'>\n";
            $response = $db->query("SELECT m.id_matchgame,c1.name as name1,c2.name as name2, m.result FROM matchgame m LEFT JOIN team c1 ON m.team_1=c1.id_team LEFT JOIN team c2 ON m.team_2=c2.id_team WHERE m.id_matchday='".$_SESSION['matchdayId']."';");
            while ($data = $response->fetch(PDO::FETCH_OBJ))
            {
                echo "  		<option value='".$data->id_matchgame."'>";
                if($data->result!=""){
                    if($data->result=="D") echo "[$title_draw] ";
                    else echo "[".$data->result."] ";
                }
                echo $data->name1." - ".$data->name2."</option>\n";
            }
            echo "	    </select>\n";
            echo "      <input type='submit' value='$title_select'>\n";
            echo "	 </form>\n";
            $response->closeCursor();
        }
    }

}

echo "</section>\n";
?>  
