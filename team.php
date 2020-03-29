<?php
/* This is the Football Predictions team section page */
/* Author : Guy Morin */

// Files to include
include("team_nav.php");

echo "<section>\n";
echo "<h2>$icon_team $title_team</h2>\n";

// Values
$error = new Errors();
$teamName="";
$teamId=$weatherCode=0;
if(isset($_POST['name'])) $teamName=$error->check("Alnum",$_POST['name'],"");
if(isset($_POST['id_team'])) $teamId=$error->check("Digit",$_POST['id_team'],"");
if(isset($_POST['weather_code'])) $weatherCode=$error->check("Digit",$_POST['weather_code'],"");

$create=$modify=$delete=0;
if(isset($_GET['create'])) $create=$error->check("Action",$_GET['create'],"");
elseif(isset($_POST['create'])) $create=$error->check("Action",$_POST['create'],"");
if(isset($_POST['modify'])) $modify=$error->check("Action",$_POST['modify'],"");
if(isset($_POST['delete'])) $delete=$error->check("Action",$_POST['delete'],"");

// Delete
if($delete==1){
        $req="DELETE FROM team WHERE id_team='".$teamId."';";
        $db->exec($req);
        $db->exec("ALTER TABLE team AUTO_INCREMENT=0;");
        popup($title_deleted,"index.php?page=team");
}
// Create
if($create==1){
    echo "<h3>$title_createATeam</h3>\n";
    // Create popup
    if($teamName!="") {
        $db->exec("ALTER TABLE team AUTO_INCREMENT=0;");
        $req="INSERT INTO team VALUES(NULL,'".$teamName."','".$weatherCode."');";
        $db->exec($req);
        popup($title_created,"index.php?page=team");
    }
    // Create form
    else {
    	echo "	    <form action='index.php?page=team' method='POST'>\n";
        echo "         <input type='hidden' name='create' value='1'>\n"; 
    	echo "	       <label>$title_name</label>\n";
    	echo "         <input type='text' name='name' value='".$teamName."'>\n";
    	echo "         <input type='submit' value='$title_create'>\n";
    	echo "	    </form>\n";   
	}
}
// Modify
elseif($modify==1){

    echo "<h3>$title_modifyATeam</h3>\n";

    // Modify popup
    if($teamName!="") {
        $req="UPDATE team SET name='".$teamName."', weather_code='".$weatherCode."' WHERE id_team='".$teamId."';";
        $db->exec($req);
        popup($title_modified,"index.php?page=team");
    }
    // Modify form
    else {
        $response = $db->query("SELECT * FROM team WHERE id_team='".$teamId."';");
    echo "	 <form action='index.php?page=team' method='POST'>\n";
    $data = $response->fetch();
    echo "      <input type='hidden' name='modify' value=1>\n";    
    /* Team ID for debugging
    echo "	    <label>Id.</label>\n";
    echo "      <input type='text' name='id_team' readonly='readonly' value='".$data['id_team']."'>\n";
    */
    echo "	    <label>$title_name</label>\n";
    echo "      <input type='text' name='name' value='".$data['name']."'>\n";
    echo "	    <label>$title_weathercode</label>\n";
    echo "      <input type='text' name='weather_code' value='".$data['weather_code']."'>\n";
    echo "      <input type='submit' value='$title_modify'>\n";
    echo "	 </form>\n";
    // Delete
    echo "	 <form action='index.php?page=team' method='POST' onsubmit='return confirm()'>\n";
    echo "      <input type='hidden' name='delete' value=1>\n";
    echo "      <input type='hidden' name='id_team' value=$teamId>\n";
    echo "      <input type='hidden' name='name' value='".$data['name']."'>\n";
    echo "      <input type='submit' value='&#9888 $title_delete &#9888'>\n"; // Bouton Supprimer
    echo "	 </form>\n";
    $response->closeCursor(); 
    }
}
echo "</section>\n";
?>
