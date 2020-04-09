<?php
/* This is the Football Predictions team section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\Errors;
use FootballPredictions\Forms;
use FootballPredictions\Section\Team;

echo "<h2>$icon_team $title_team</h2>\n";

// Values
$teamName = "";
$teamId = $weatherCode = 0;
isset($_POST['name'])           ? $teamName = $error->check("Alnum",$_POST['name']) : null;
isset($_POST['id_team'])        ? $teamId = $error->check("Digit",$_POST['id_team']) : null;
isset($_POST['weather_code'])   ? $weatherCode = $error->check("Digit",$_POST['weather_code']) : null;

$create = $modify = $delete = 0;
isset($_GET['create'])          ? $create = $error->check("Action",$_GET['create']) : null;
$create == 0 && isset($_POST['create']) ? $create = $error->check("Action",$_POST['create']) : null;
isset($_POST['modify'])         ? $modify = $error->check("Action",$_POST['modify']) : null;
isset($_POST['delete'])         ? $delete = $error->check("Action",$_POST['delete']) : null;

// Delete
if($delete==1){
        $req="DELETE FROM team WHERE id_team=:id_team;";
        $pdo->prepare($req,[
            'id_team' => $teamId
        ]);
        $pdo->alterAuto('team');
        popup($title_deleted,"index.php?page=team");
}
// Create
if($create == 1){
    echo "<h3>$title_createATeam</h3>\n";
    if($pdo->findName('team', $teamName))  $error->setError($title_errorExists);
    // Create popup
    elseif($teamName!=""){
        $pdo->alterAuto('team');
        $req="INSERT INTO team VALUES(NULL,:$teamName,:weatherCode);";
        $pdo->prepare($req,[
            'teamName' => $teamName,
            'weatherCode' => $weatherCode
        ]);
        popup($title_created,"index.php?page=team");
    }
    // Create form
    else {
        echo Team::createForm($pdo, $error, $form, $teamName, $weatherCode);
	}
}
// Modify
elseif($modify==1){

    echo "<h3>$title_modifyATeam</h3>\n";

    // Modify popup
    if($teamName!=""){
        $req="UPDATE team SET name=:name, weather_code=:weather_code
        WHERE id_team=:id_team;";
        $pdo->prepare($req,[
            'name' => $teamName,
            'weather_code' => $weatherCode,
            'id_team' => $teamId
        ]);
        popup($title_modified,"index.php?page=team");
    }
    // Modify form
    else {
        $req = "SELECT * FROM team WHERE id_team=:id_team;";
        $data = $pdo->prepare($req,[
            'id_team' => $teamId
        ]);
        
        echo Team::modifyForm($pdo, $data, $error, $form, $teamId);
    }
}
?>
