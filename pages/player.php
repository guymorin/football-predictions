<?php
/* This is the Football Predictions player section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\Errors;
use FootballPredictions\Forms;

echo "<h2>$icon_player $title_player</h2>\n";

// Values
$playerId=$teamId=0;
$playerName=$playerFirstname=$playerPosition="";
isset($_POST['id_player'])      ? $playerId=$error->check("Digit",$_POST['id_player']) : null;
isset($_POST['name'])           ? $playerName=$error->check("Alnum",$_POST['name']) : null;
isset($_POST['firstname'])      ? $playerFirstname=$error->check("Alnum",$_POST['firstname']) : null;
isset($_POST['position'])       ? $playerPosition=$error->check("Position",$_POST['position']) : null;
isset($_POST['id_team'])        ? $teamId=$error->check("Digit",$_POST['id_team']) : null;
$create=$modify=$delete=0;
isset($_GET['create'])          ? $create=$error->check("Action",$_GET['create']) : null;
isset($_POST['create'])         ? $create=$error->check("Action",$_POST['create']) : null;
isset($_GET['modify'])          ? $modify=$error->check("Action",$_GET['modify']) : null;
isset($_POST['modify'])         ? $modify=$error->check("Action",$_POST['modify']) : null;
isset($_POST['delete'])         ? $delete=$error->check("Action",$_POST['delete']) : null;

// Delete
if($delete==1){
        $req="DELETE FROM season_team_player 
        WHERE id_season=:id_season 
        AND id_team=:id_team 
        AND id_player=:id_player;";
        $req.="DELETE FROM player 
        WHERE id_player=:id_player;";
        $data=$pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId'],
            'id_team' => $teamId,
            'id_player' => $playerId
        ]);
        $pdo->alterAuto('season_team_player');
        $pdo->alterAuto('player');
        popup($title_deleted,"index.php?page=player");
}
// Create
elseif($create==1){
    echo "<h3>$title_createAPlayer</h3>\n";
    // Create popup
    if(($playerName!="")&&($playerFirstname==$_POST['firstname'])&&($playerPosition!="")&&($teamId>0)){
        $pdo->alterAuto('season_team_player');
        $pdo->alterAuto('player');
        $req1="INSERT INTO player 
        VALUES(NULL,:name,:firstname,:position);";
        $pdo->prepare($req1,[
            'name' => $playerName,
            'firstname' => $playerFirstname,
            'position' => $playerPosition
        ]);
        $playerId=$pdo->lastInsertId();
        $req2="INSERT INTO season_team_player VALUES(NULL,:id_season,:id_team,:id_player);";
        $pdo->prepare($req2,[
            'id_season' => $_SESSION['seasonId'],
            'id_team' => $teamId,
            'id_player' => $playerId
        ]);
        popup($title_created,"index.php?page=player");
    }
    // Create form
    else {
        echo $error->getError();
    	echo "<form action='index.php?page=player' method='POST'>\n";
        echo $form->inputAction('create'); 

        echo $form->input($title_name, 'name') . $form->input($title_firstname, 'firstname');
        echo "<br />\n";
        
        echo $form->inputRadioPosition();
        echo "<br />\n";
        
        $req = "SELECT id_team, name FROM team ORDER BY name;";
        $data = $pdo->prepare($req,null,true);
        echo $form->selectTeam($data);
        
        echo $form->submit($title_create);
    	echo "</form>\n";
	}
}
// Modify
elseif($modify==1){
    echo "<h3>$title_modifyAPlayer</h3>\n";
    // Modify popup
    if(($playerName!="")&&($playerFirstname==$_POST['firstname'])&&($playerPosition!="")&&($teamId>0)){
        
        // Check if the player is known in Season Team Player table
        $req = "SELECT COUNT(*) as nb
        FROM season_team_player
        WHERE id_season=:id_season
        AND id_team=:id_team
        AND id_player=:id_player;";
        $data = $pdo->prepare($req,[
            'id_season' => $_SESSION['seasonId'],
            'id_team' => $teamId,
            'id_player' => $playerId
        ]);    
        
        $req="UPDATE player 
        SET name=:name, firstname=:firstname, position=:position  
        WHERE id_player=:id_player;";
        if($data[0]==0){
            $req.="INSERT INTO season_team_player 
            VALUES(NULL,:id_season,:id_team,:id_player);";
        }
        if($data[0]==1){
            $req.="UPDATE season_team_player SET id_season=:id_season,id_team=:id_team WHERE id_player=:id_player;";
        }
        $pdo->prepare($req,[
            'name' => $playerName,
            'firstname' => $playerFirstname,
            'position' => $playerPosition,
            'id_season' => $_SESSION['seasonId'],
            'id_team' => $teamId,
            'id_player' => $playerId
        ]);
        
        popup($title_modified,"index.php?page=player");
    }
    // Modify form
    elseif($playerId!=0){
        $req ="SELECT j.id_player, j.name, j.firstname, j.position, scj.id_team 
        FROM player j 
        LEFT JOIN season_team_player scj ON j.id_player=scj.id_player 
        LEFT JOIN team c ON scj.id_team=c.id_team 
        WHERE j.id_player=:id_player;";
        $data = $pdo->prepare($req,[
            'id_player' => $playerId
        ]);
        
        $playerId = $data->id_player;
        $playerName = $data->name;
        $playerFirstname = $data->firstname;
        $teamId = $data->id_team;
        echo $error->getError();
        echo "<form action='index.php?page=player' method='POST'>\n";
        $form->setValues($data);
        echo $form->inputAction('modify');  
        echo $form->inputHidden('id_player',$playerId);
        echo $form->input($title_name,'name');
        echo $form->input($title_firstname,'firstname');
        echo "<br />\n";
        
        echo $form->inputRadioPosition($data);
        echo "<br />\n";
        
        $req = "SELECT id_team, name FROM team ORDER BY name;";
        $data = $pdo->prepare($req,null,true);       
        echo $form->selectTeam($data,$teamId);
        
        echo $form->submit($title_modify);
        echo "</form>\n";
        // Delete form
        echo "<form action='index.php?page=player' method='POST' onsubmit='return confirm()'>\n";
        echo $form->inputAction('delete');
        echo $form->inputHidden('id_team',$teamId);
        echo $form->inputHidden('id_player',$playerId);
        echo $form->inputHidden('name',$playerName);
        echo $form->inputHidden('firstname',$playerFirstname);
        echo $form->submit("&#9888 $title_delete &#9888");
        echo "</form>\n";
          
    }
    // Select form
    else {
        echo "<form action='index.php?page=player' method='POST'>\n";
        echo $form->inputAction('modify');
        
        $req = "SELECT id_player, name, firstname FROM player ORDER BY name, firstname;";
        $data = $pdo->prepare($req,null,true);
        echo $form->selectPlayer($data);
        
        echo $form->submit($title_select);
        echo "</form>\n";
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
    $data = $pdo->prepare($req,null,true);
    echo "<p>\n";
    echo "  <table>\n";
    echo "      <tr><th></th><th>$title_player</th><th>$title_team</th><th>$title_teamOfTheWeek</th><th>$title_ratingAverage</th></tr>\n";
    $counterPodium = 0;
    $icon = "&#129351;"; // Gold medal
    foreach ($data as $d)
        {
        $counterPodium++;
        if($counterPodium==2) $icon="&#129352;"; // Silver medal
        else $icon="&#129353;"; // Bronze medal
        
            echo "      <td><strong>".$counterPodium."</strong></td>\n";
            echo "      <td>".$icon." <strong>".mb_strtoupper($d->name,'UTF-8')." ".$d->firstname."</strong></td>\n";
            echo "      <td>".$d->team."</td>\n";
            echo "      <td>".$d->nb."</td>\n";
            echo "      <td>".round($d->rating,1)."</td>\n";
            echo "  </tr>\n";
        }
    echo "  </table>\n";
    echo "</p>\n";
     
    
    echo "<h3>$title_bestPlayersByTeam</h3>\n";
    
    $req = "SELECT COUNT(e.rating) as nb,AVG(e.rating) as rating,c.name as team,j.name,j.firstname 
    FROM player j
    LEFT JOIN season_team_player scj ON j.id_player=scj.id_player
    LEFT JOIN team c ON  c.id_team=scj.id_team 
    LEFT JOIN teamOfTheWeek e ON e.id_player=j.id_player 
    GROUP BY team,j.name,j.firstname 
    ORDER BY team ASC, nb DESC, rating DESC, j.name,j.firstname ASC";
    $data = $pdo->prepare($req,null,true);
    echo "  <table>\n";
    echo "      <tr><th>$title_team</th><th>$title_player</th><th>$title_teamOfTheWeek</th><th>$title_ratingAverage</th></tr>\n";
    $counter = "";
    foreach ($data as $d)
        {
            echo "      <td>";
            if($counter!=$d->team){
                $counterPodium = 0;
                echo "<strong>".$d->team."</strong>";
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
            if($icon!="") echo $icon." <strong>".mb_strtoupper($d->name,'UTF-8')." ".$d->firstname."</strong>";
            else echo mb_strtoupper($d->name,'UTF-8')." ".$d->firstname;
            echo "</td><td>".$d->nb."</td><td>".round($d->rating,1)."</td>\n";
            echo "  </tr>\n";
            $counter=$d->team;
        }
    
    echo "  </table>\n";
}
?>
