<?php
/* This is the Football Predictions team section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\Errors;
use FootballPredictions\Forms;

echo "<h2>$icon_team $title_team</h2>\n";

// Values
$teamName="";
$teamId=$weatherCode=0;
isset($_POST['name']) ? $teamName=$error->check("Alnum",$_POST['name']) : null;
isset($_POST['id_team']) ? $teamId=$error->check("Digit",$_POST['id_team']) : null;
isset($_POST['weather_code']) ? $weatherCode=$error->check("Digit",$_POST['weather_code']) : null;

$create=$modify=$delete=0;
isset($_GET['create']) ? $create=$error->check("Action",$_GET['create']) : null;
if(create==0) isset($_POST['create']) ? $create=$error->check("Action",$_POST['create']) : null;
isset($_POST['modify']) ? $modify=$error->check("Action",$_POST['modify']) : null;
isset($_POST['delete']) ? $delete=$error->check("Action",$_POST['delete']) : null;

// Delete
if($delete==1){
        $req="DELETE FROM team WHERE id_team=:id_team;";
        $response = $db->prepare($req);
        $response->execute([
            'id_team' => $teamId
        ]);
        $db->exec("ALTER TABLE team AUTO_INCREMENT=0;");
        popup($title_deleted,"index.php?page=team");
}
// Create
if($create==1){
    echo "<h3>$title_createATeam</h3>\n";
    // Create popup
    if($teamName!=""){
        $db->exec("ALTER TABLE team AUTO_INCREMENT=0;");
        $req="INSERT INTO team VALUES(NULL,:$teamName,:weatherCode);";
        $response = $db->prepare($req);
        $response->execute([
            'teamName' => $teamName,
            'weatherCode' => $weatherCode
        ]);
        popup($title_created,"index.php?page=team");
    }
    // Create form
    else {
    	echo "<form action='index.php?page=team' method='POST'>\n";
    	echo $error->getError();
        echo $form->inputAction('create'); 
        $form->setValue('name', $teamName);
        echo $form->input($title_name, name);
    	echo $form->submit($title_create);
    	echo "</form>\n";   
	}
}
// Modify
elseif($modify==1){

    echo "<h3>$title_modifyATeam</h3>\n";

    // Modify popup
    if($teamName!=""){
        $req="UPDATE team SET name=:name, weather_code=:weather_code
        WHERE id_team=:id_team;";
        $response = $db->prepare($req);
        $response->execute([
            'name' => $teamName,
            'weather_code' => $weatherCode,
            'id_team' => $teamId
        ]);
        popup($title_modified,"index.php?page=team");
    }
    // Modify form
    else {
        $response = $db->prepare("SELECT * FROM team WHERE id_team=:id_team;");
        $response->execute([
            'id_team' => $teamId
        ]);
        $data = $response->fetch(PDO::FETCH_OBJ);
        echo "<form action='index.php?page=team' method='POST'>\n";
        echo $error->getError();
        echo $form->inputAction('modify');
        echo $form->input($title_name, 'name');
        echo $form->input($title_weathercode, 'weather_code');
        echo $form->submit($title_modify);
        echo "</form>\n";
        // Delete
        echo "<form action='index.php?page=team' method='POST' onsubmit='return confirm()'>\n";
        echo $error->getError();
        echo $form->inputAction('delete');
        echo $form->inputHidden('id_team',$teamId);
        echo $form->inputHidden('name',$data->name);
        echo $form->submit('&#9888 $title_delete &#9888');
        echo "</form>\n";
        $response->closeCursor(); 
    }
}
?>
