<?php
/* This is the Football Predictions season section page */
/* Author : Guy Morin */

// Files to include
require("season_nav.php");

echo "<section>\n";

// Values
$error = new Errors();
$seasonId=0;
$seasonName="";
if(isset($_POST['id_season'])) $seasonId=$error->check("Digit",$_POST['id_season']);
if(isset($_POST['name'])) $seasonName=$error->check("Alnum",$_POST['name']);
$create=0;
$modify=0;
$delete=0;
if(isset($_GET['create'])) $create=$error->check("Digit",$_GET['create']);
if((isset($_POST['create']))&&($_POST['create']==1)) $create=$error->check("Digit",$_POST['create']);
if((isset($_POST['modify']))&&($_POST['modify']==1)) $modify=$error->check("Digit",$_POST['modify']);
if((isset($_POST['delete']))&&($_POST['delete']==1)) $delete=$error->check("Digit",$_POST['delete']);


// First, select a season
if(
    (empty($_SESSION['seasonId']))
    &&($create==0)
    &&($modify==0)
    &&($delete==0)
){
    echo "      <ul class='menu'>\n";
    echo "        <h2>$icon_season $title_season</h2>\n";
    
    $response = $db->query("SELECT * FROM season ORDER BY name;");
    $list="";
    if($response->rowCount()>0){
        // Select form
    $list.="  	<form action='index.php?page=championship' method='POST'>\n";
    $list.="        <label>$title_selectTheSeason :</label><br />\n";
    $list.="  	  <select name='seasonSelect' onchange='submit()'>\n";
    $list.="  		<option value='0'>...</option>\n";
    

    while ($data = $response->fetch(PDO::FETCH_OBJ))
    {
        $list.="  		<option value='".$data->id_season.",".$data->name."'>".$data->name."</option>\n";
    }
    $list.="	       </select>\n";
    $list.="         <br /><noscript><input type='submit' value='$title_select'></noscript>\n";
    $list.="	     </form>\n";
    
    // Quick nav button
    $response = $db->query("SELECT * FROM season ORDER BY id_season DESC;");
    
    $data = $response->fetch(PDO::FETCH_OBJ);
    echo "  	<form action='index.php?page=championship' method='POST'>\n";
    echo "      <label>$title_quickNav :</label><br />\n";
    echo "          <input type='hidden' name='seasonSelect' value='".$data->id_season.",".$data->name."'>\n";
    echo "          <input type='submit' value='$icon_quicknav ".$data->name."'>\n";
    echo "      </form>\n";
    
    echo $list;
    }
    // No season
    else {
        echo "      <h2>$title_noSeason</h2>\n";
    }
    echo "      </ul>\n";
    $response->closeCursor();
}
// Popup if needed
// Delete
elseif($delete==1){
        $req="DELETE FROM season WHERE id_season='".$seasonId."';";
        $db->exec($req);
        $db->exec("ALTER TABLE season AUTO_INCREMENT=0;");
        popup($title_deleted,"index.php?page=season");
}
// Create
elseif($create==1){
    echo "<h2>$title_createASeason</h2>\n";
    // Create popup
    if($seasonName!="") {
        $db->exec("ALTER TABLE season AUTO_INCREMENT=0;");
        $req="INSERT INTO season VALUES(NULL,'".$seasonName."');";
        $db->exec($req);
        popup($title_created,"index.php?page=season");
    }
    // Create form
    else {
	echo "	    <form action='index.php?page=season' method='POST'>\n";
	echo "      <div class='error'>".$error->getError()."</div>\n";
    echo "      <input type='hidden' name='create' value='1'>\n"; 
	echo "	    <label>$title_name :</label>\n";
	echo "      <input type='text' name='name' value='".$seasonName."'>\n";
	echo "      <input type='submit' value='$title_create'>\n";
	echo "	    </form>\n";   
	}
}
// Modify
elseif($modify==1){
    echo "<h2>$title_modifyASeason</h2>\n";
    // Modify popup
    if($seasonName!="") {
        $req="UPDATE season SET name='".$seasonName."' WHERE id_season='".$seasonId."';";
        $db->exec($req);
        popup($title_modified,"index.php?page=season");
    }
    // Modify form
    else {
    $response = $db->query("SELECT * FROM season WHERE id_season='".$seasonId."';");
    echo "	 <form action='index.php?page=season' method='POST'>\n";
    echo "      <div class='error'>".$error->getError()."</div>\n";
    $data = $response->fetch(PDO::FETCH_OBJ);
    echo "      <input type='hidden' name='modify' value=1>\n";    
    echo "      <input type='hidden' name='id_season' readonly='readonly' value='".$data->id_season."'>\n";
    echo "	    <label>$title_name :</label>\n";
    echo "      <input type='text' name='name' value='".$data->name."'>\n";
    echo "      <input type='submit' value='$title_modify'>\n";
    echo "	 </form>\n";
    // Delete form
    echo "	 <form action='index.php?page=season' method='POST' onsubmit='return confirm()'>\n";
    echo "      <input type='hidden' name='delete' value=1>\n";
    echo "      <input type='hidden' name='id_season' value=$seasonId>\n";
    echo "      <input type='hidden' name='name' value='".$data->name."'>\n";
    echo "      <input type='submit' value='&#9888 $title_delete &#9888'>\n";
    echo "	 </form>\n";
    $response->closeCursor();
    }
}
else {
    echo "<h2>$icon_season $title_season</h2>\n";
    echo "<h3>".$_SESSION['seasonName']."</h3>\n";
    $response = $db->prepare("SELECT c.name, COUNT(*) as nb 
    FROM championship c
    LEFT JOIN season_championship_team scc ON c.id_championship=scc.id_championship
    WHERE scc.id_season=:id_season
    GROUP BY c.name
    ORDER BY c.name");
    $response->execute([
        'id_season' => $_SESSION['seasonId']
    ]);
    echo "<table>\n";
    echo "  <tr>\n";
    echo "      <th>$title_championship</th>\n";
    echo "      <th>$title_teams</th>\n";
    echo "  </tr>\n";
    while ($data = $response->fetch(PDO::FETCH_OBJ))
    {
        echo "  <tr>\n";
        echo "      <td>$data->name</td>\n";
        echo "      <td>$data->nb</td>\n";
        echo "  </tr>\n";
    }
    echo "</table>\n";
}
?>
    </section>
    
