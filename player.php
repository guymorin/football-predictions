<?php
/* This is the Football Predictions player section page */
/* Author : Guy Morin */

// Files to include
include("player_nav.php");

echo "<section>\n";
echo "<h2>$icon_player $title_player</h2>\n";
// Values
$playerId=$teamId=0;
$playerName=$playerFirstname=$playerPosition="";
$playerId=checkDigit($_POST['id_player']);
if(isset($_POST['name'])) $playerName=$_POST['name'];
if(isset($_POST['firstname'])) $playerFirstname=$_POST['firstname'];
if(isset($_POST['position'])) $playerPosition=$_POST['position'];
if(isset($_POST['id_team'])) $teamId=$_POST['id_team'];
$create=0;
$modify=0;
$delete=0;
if((isset($_GET['create']))&&($_GET['create']==1)) $create=$_GET['create'];
if((isset($_POST['create']))&&($_POST['create']==1)) $create=$_POST['create'];
if(isset($_GET['modify'])) $modify=$_GET['modify'];
if((isset($_POST['modify']))&&($_POST['modify']==1)) $modify=$_POST['modify'];
if((isset($_POST['delete']))&&($_POST['delete']==1)) $delete=$_POST['delete'];

// Delete
if($delete==1){
        $req="DELETE FROM season_team_player WHERE id_season='".$_SESSION['seasonId']."' AND id_team='".$teamId."' AND id_player='".$playerId."';";
        $req.="DELETE FROM player WHERE id_player='".$playerId."';";
        $db->exec($req);
        $db->exec("ALTER TABLE season_team_player AUTO_INCREMENT=0");
        $db->exec("ALTER TABLE player AUTO_INCREMENT=0");
        popup($title_deleted,"index.php?page=player");
}
// Create
elseif($create==1){
    echo "<h3>$title_createAPlayer</h3>\n";
    // Create popup
    if($playerName!="") {
        $db->exec("ALTER TABLE season_team_player AUTO_INCREMENT=0;");
        $db->exec("ALTER TABLE player AUTO_INCREMENT=0;");
        $req1="INSERT INTO player VALUES(NULL,'".$playerName."','".$playerFirstname."','".$playerPosition."');";
        $db->exec($req1);
        $playerId=$db->lastInsertId();
        $req2="INSERT INTO season_team_player VALUES(NULL,'".$_SESSION['seasonId']."','".$teamId."','".$playerId."');";
        $db->exec($req2);
        popup($title_created,"index.php?page=player");
    }
    // Create form
    else {    
    	echo "	 <form action='index.php?page=player' method='POST'>\n";
        echo "      <input type='hidden' name='create' value='1'>\n"; 
    	echo "	    <label>$title_name</label>\n";
    	echo "      <input type='text' name='name' value='".$playerName."'>\n";
    	echo "	    <label>$title_firstname</label>\n";
    	echo "      <input type='text' name='firstname' value='".$playerName."'>\n";
    	echo "	    <p><label>$title_position</label>\n";
    	echo "      <input type='radio' name='position' id='Goalkeeper' value='Goalkeeper'><label for='Goalkeeper'>$title_goalkeeper</label>\n";
    	echo "      <input type='radio' name='position' id='Defender' value='Defender'><label for='Defender'>$title_defender</label>\n";
    	echo "      <input type='radio' name='position' id='Midfield' value='Midfield'><label for='Midfield'>$title_midfielder</label>\n";
    	echo "      <input type='radio' name='position' id='Forward' value='Forward'><label for='Forward'>$title_forward</label></p>\n";
        echo "	    <p><label>Club</label>\n";
        echo "     <select multiple size='10' name='id_team'>\n";
        $response = $db->query("SELECT * FROM team ORDER BY name;");
        while ($data = $response->fetch())
        {
            echo "  		<option value='".$data['id_team']."'>".$data['name']."</option>\n";
        }
        echo "	   </select></p>\n";
    	echo "     <input type='submit' value='$title_create'>\n";
    	echo "	 </form>\n";
	}
}
// Modify
elseif($modify==1){
    echo "<h3>$title_modifyAPlayer</h3>\n";
    // Modify popup
    if($playerName!="") {
        $req="UPDATE player SET name='".$playerName."', firstname='".$playerFirstname."' WHERE id_player='".$playerId."';";
        
        $response = $db->query("SELECT COUNT(*) as nb FROM season_team_player WHERE id_season='".$_SESSION['seasonId']."' AND id_team='".$teamId."' AND id_player='".$playerId."';");
        $data = $response->fetch();
        $response->closeCursor();
        
        if($data[0]==0){
            $req.="INSERT INTO season_team_player VALUES(NULL,'".$_SESSION['seasonId']."','".$teamId."','".$playerId."');";
        }
        if($data[0]==1){
            $req.="UPDATE season_team_player SET id_season='".$_SESSION['seasonId']."',id_team='".$teamId."' WHERE id_player='".$playerId."';";
        }
        $db->exec($req);
        popup($title_modified,"index.php?page=player");
    }
    // Modify form
    elseif($playerId!=0) {
        $req ="SELECT j.id_player, j.name, j.firstname, j.position, scj.id_team FROM player j LEFT JOIN season_team_player scj ON j.id_player=scj.id_player LEFT JOIN team c ON scj.id_team=c.id_team WHERE j.id_player='".$playerId."';";
        $response = $db->query($req);
        echo "	 <form action='index.php?page=player' method='POST'>\n";
        $data = $response->fetch();
        $playerId = $data['id_player'];
        $playerName = $data['name'];
        $playerFirstname = $data['firstname'];
        $teamId = $data['id_team'];
        echo "      <input type='hidden' name='modify' value=1>\n";    

        echo "      <input type='hidden' name='id_player' readonly value='".$playerId."'>\n";
    
        echo "	    <label>$title_name</label>\n";
        echo "      <input type='text' name='name' value='".$playerName."'>\n";
        echo "	    <label>Prénom</label>\n";
        echo "      <input type='text' name='firstname' value='".$playerFirstname."'>\n";
        
    	echo "	    <p><label>Poste</label>\n";
    	echo "      <input type='radio' name='position' id='Goalkeeper' value='Goalkeeper'";
            if ($data['position']=="Goalkeeper") echo " checked";
    	echo "><label for='Goalkeeper'>$title_goalkeeper</label>\n";
    	echo "     <input type='radio' name='position' id='Defender' value='Defender'";
            if ($data['position']=="Defender") echo " checked";
    	echo "><label for='Defender'>$title_defender</label>\n";
    	echo "     <input type='radio' name='position' id='Midfielder' value='Midfielder'";
            if ($data['position']=="Midfielder") echo " checked";	
    	echo "><label for='Midfielder'>$title_midfielder</label>\n";
    	echo "     <input type='radio' name='position' id='Forward' value='Forward'";
            if ($data['position']=="Forward") echo " checked";	
    	echo "><label for='Forward'>$title_forward</label></p>\n";
    	
        echo "	    <p><label>$title_team</label>\n";
        echo "      <select multiple size='10' name='id_team'>\n";
        $response = $db->query("SELECT * FROM team ORDER BY name;");
        while ($data = $response->fetch())
        {
            echo "  		<option value='".$data['id_team']."'";
            if($data['id_team']==$teamId) echo " selected";
            echo ">".$data['name']."</option>\n";
        }
        echo "	    </select></p>\n";
        echo "      <input type='submit' value='$title_modify'>\n";
        echo "	 </form>\n";
        // Delete form
        echo "	 <form action='index.php?page=player' method='POST' onsubmit='return confirm()'>\n";
        echo "      <input type='hidden' name='delete' value=1>\n";
        echo "      <input type='hidden' name='id_team' value=$teamId>\n";
        echo "      <input type='hidden' name='id_player' value=$playerId>\n";
        echo "      <input type='hidden' name='name' value='".$playerName."'>\n";
        echo "      <input type='hidden' name='firstname' value='".$playerFirstname."'>\n";
        echo "      <input type='submit' value='&#9888 $title_delete &#9888'>\n";
        echo "	 </form>\n";
        $response->closeCursor();  
    }
    // Select form
    else {
        echo "   <form action='index.php?page=player' method='POST'>\n";             // Modifier
        echo "      <input type='hidden' name='modify' value='1'>\n"; 
        echo "      <label>$title_selectThePlayer :</label>\n";                                    
        echo "  	<select multiple size='10' name='id_player'>\n";
        $response = $db->query("SELECT * FROM player ORDER BY name, firstname");
        while ($data = $response->fetch())
        {
            echo "  		<option value='".$data['id_player']."'>".mb_strtoupper($data['name'],'UTF-8')." ".$data['firstname']."</option>\n";
        }
        echo "	    </select>\n";
        echo "      <input type='submit' value='$title_select'>\n";
        echo "	 </form>\n";
    }
}
// Default page (best players)
else {

    echo "<h3>$title_bestPlayers</h3>\n";
    
    $req = "SELECT COUNT(e.rating) as nb,AVG(e.rating) as rating,c.name as team,j.name,j.firstname 
    FROM player j
    LEFT JOIN season_team_player scj ON j.id_player=scj.id_player 
    LEFT JOIN team c ON  c.id_team=scj.id_team 
    LEFT JOIN teamOfTheWeek e ON e.id_player=j.id_player 
    GROUP BY team, j.name,j.firstname 
    ORDER BY nb DESC, rating DESC,j.name,j.firstname LIMIT 0,3";
    $response = $db->query($req);
    echo "  <p><table>\n";
    echo "      <tr><th></th><th>$title_player</th><th>$title_team</th><th>$title_teamOfTheWeek</th><th>$title_ratingAverage</th></tr>\n";
    $counterPodium = 0;
    $icon = "&#129351;"; // Gold medal
    while ($data = $response->fetch())
        {
        $counterPodium++;
        if($counterPodium==2) $icon="&#129352;"; // Silver medal
        else $icon="&#129353;"; // Bronze medal
        
            echo "      <td><strong>".$counterPodium."</strong></td>\n";
            echo "      <td>".$icon." <strong>".mb_strtoupper($data['name'],'UTF-8')." ".$data['firstname']."</strong></td>\n";
            echo "      <td>".$data['team']."</td>\n";
            echo "      <td>".$data['nb']."</td>\n";
            echo "      <td>".round($data['rating'],1)."</td>\n";
            echo "  </tr>\n";
        }
    echo "  </table></p>\n";
    $response->closeCursor(); 
    
    echo "<h3>$title_bestPlayersByTeam</h3>\n";
    
    $req = "SELECT COUNT(e.rating) as nb,AVG(e.rating) as rating,c.name as team,j.name,j.firstname 
    FROM player j
    LEFT JOIN season_team_player scj ON j.id_player=scj.id_player
    LEFT JOIN team c ON  c.id_team=scj.id_team 
    LEFT JOIN teamOfTheWeek e ON e.id_player=j.id_player 
    GROUP BY team,j.name,j.firstname 
    ORDER BY team ASC, nb DESC, rating DESC, j.name,j.firstname ASC";
    $response = $db->query($req);
    echo "  <table>\n";
    echo "      <tr><th>$title_team</th><th>$title_player</th><th>$title_teamOfTheWeek</th><th>$title_ratingAverage</th></tr>\n";
    $counter = "";
    while ($data = $response->fetch())
        {
            echo "      <td>";
            if($counter!=$data['team']){
                $counterPodium = 0;
                echo "<strong>".$data['team']."</strong>";
            }
            
            $counterPodium++;
            switch($counterPodium){
                case 1:
                    $icon = "&#129351;"; // gold medal
                    break;
                case 2:
                    $icon="&#129352;"; // silver medal
                    break;
                case 3:
                    $icon="&#129353;"; // bronze medal
                    break;
                default:
                    $icon="";
            }
            
            echo "</td><td>";
            if($icon!="") echo $icon." <strong>".mb_strtoupper($data['name'],'UTF-8')." ".$data['firstname']."</strong>";
            else echo mb_strtoupper($data['name'],'UTF-8')." ".$data['firstname'];
            echo "</td><td>".$data['nb']."</td><td>".round($data['rating'],1)."</td>\n";
            echo "  </tr>\n";
            $counter=$data['team'];
        }
    $response->closeCursor();
    echo "  </table>\n";
}
echo "</section>\n";
?>