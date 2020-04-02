<?php
/* This is the Football Predictions team of the week section page */
/* Author : Guy Morin */

// Files to include
require("../include/changeMD.php");
require("matchday_nav.php");

echo "<section>\n";
echo "<h2>$icon_matchday $title_matchday ".$_SESSION['matchdayNum']."</h2>\n";

// Only if a matchday is selected
if(isset($_SESSION['matchdayId'])){

    changeMD($db,"teamOfTheWeek");
    
    // Popup modified
    if($teamOfTheWeek==1){
        $db->exec("ALTER TABLE teamOfTheWeek AUTO_INCREMENT=0;");
        $req="";
        foreach($deletePlayer as $d){
            $req="DELETE FROM teamOfTheWeek WHERE id_matchday='".$_SESSION['matchdayId']."' AND id_player='".$d."';";
            $db->exec($req);
        }
        $db->exec("ALTER TABLE teamOfTheWeek AUTO_INCREMENT=0;");
        $req="";
        foreach($val as $k=>$v){
            $v=$error->check("Digit",$v);
            if(($v>0)&&(!in_array($k,$deletePlayer))){
                
                $response = $db->query("SELECT COUNT(*) as nb FROM teamOfTheWeek WHERE id_matchday='".$_SESSION['matchdayId']."' AND id_player='".$k."';");
                $data = $response->fetch(PDO::FETCH_OBJ);
                $response->closeCursor();
                
                if($data[0]==0) {
                    $req.="INSERT INTO teamOfTheWeek VALUES(NULL,'".$_SESSION['matchdayId']."','".$k."','".$v."');";
                }
                if($data[0]==1) {
                    $req.="UPDATE teamOfTheWeek SET rating='".$v."' WHERE id_matchday='".$_SESSION['matchdayId']."' AND id_player='".$k."';";
                }
            
            }
        } 
        $db->exec($req);
        popup($title_modified,"index.php?page=teamOfTheWeek");
    }
    // Modify form
    else {
        echo "<h3>$title_teamOfTheWeek</h3>\n";
        $counter=0;
        $req = "SELECT j.id_player,j.name,j.firstname,e.rating FROM teamOfTheWeek e LEFT JOIN player j ON e.id_player=j.id_player WHERE id_matchday='".$_SESSION['matchdayId']."' ORDER BY j.position,j.name,j.firstname;";
        $response = $db->query($req); 
        
        echo "	 <form action='index.php?page=teamOfTheWeek' method='POST' onsubmit='return confirm();'>\n";
        echo $error->getError();
        echo "      <input type='hidden' name='teamOfTheWeek' value='1'>\n";
        
        echo "   <table id='teamOfTheWeek'>\n";
        echo "    <tr><th> </th><th>$title_player</th><th>$title_rating</th><th>&#10060;</th></tr>\n";

        while ($data = $response->fetch(PDO::FETCH_OBJ))
        {
            $counter++;
            echo "  	<tr><td>".$counter."</td>";
            echo "<td><input type='hidden' name='id_player[]' value='".$data->id_player."'>".mb_strtoupper($data->name,'UTF-8')." ".$data->firstname."</td>";
            echo "<td><input type='text' name='rating[]' size='3' value='".$data->rating."'</td><td><input type='checkbox' name='delete[]' value='".$data->id_player."'></td></tr>\n";
        }
        
        $req = "SELECT j.id_player, j.name, j.firstname, j.position, c.name as team 
        FROM player j
        LEFT JOIN season_team_player scj ON scj.id_player=j.id_player 
        LEFT JOIN season_championship_team scc ON scc.id_team=scj.id_team 
        LEFT JOIN team c ON c.id_team=scj.id_team
        WHERE scc.id_season='".$_SESSION['seasonId']."' 
        AND scc. id_championship='".$_SESSION['championshipId']."' 
        ORDER BY j.name, j.firstname;";
       
        $playersLeft=11-$counter;
        for($i=0;$i<$playersLeft;$i++){
            $counter++;
            $response = $db->query($req);
            echo "  	<tr>";
            echo "<td>".$counter."</td>";
            echo "<td><select name='id_player[]'>\n";
            echo "  <option value=''>...</option>\n";
            while ($data = $response->fetch(PDO::FETCH_OBJ))
            {

                echo "  <option value='".$data->id_player."'>".mb_strtoupper($data->name,'UTF-8')." ".$data->firstname." [".$data->team."]";
                echo "</option>\n";
            }
            echo "</select>\n";
            echo "</td><td><input type='text' name='rating[]' value=''></td><td> </td></tr>\n";
        }
        echo "  </table>\n";
        echo "      <input type='submit'>\n";
        echo "	 <form>\n";
        $response->closeCursor();   
    
    }
}
echo "</section>\n";
