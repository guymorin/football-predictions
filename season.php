<?php
/* This is the Football Predictions season section page */
/* Author : Guy Morin */

// Files to include
include("season_nav.php");

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
if((isset($_GET['create']))&&($_GET['create']==1)) $create=$_GET['create'];
if((isset($_POST['create']))&&($_POST['create']==1)) $create=$_POST['create'];
if((isset($_POST['modify']))&&($_POST['modify']==1)) $modify=$_POST['modify'];
if((isset($_POST['delete']))&&($_POST['delete']==1)) $delete=$_POST['delete'];

// Popup if needed
// Delete
if($delete==1){
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
	echo "	    <label>$title_name</label>\n";
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
    echo "	 <label>$title_name</label>\n";
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
    echo "<h2>$title_season</h2>\n";
    echo "<h3>".$_SESSION['seasonName']."</h3>\n";
}
?>
    </section>
    
