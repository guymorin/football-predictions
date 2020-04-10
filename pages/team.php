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
if($delete == 1){
    Team::deletePopup($pdo, $teamId);
}
// Create
if($create == 1){
    echo "<h3>$title_createATeam</h3>\n";
    if($pdo->findName('team', $teamName))  $error->setError($title_errorExists);
    elseif($teamName!="") Team::createPopup($pdo, $teamName, $weatherCode);
    echo Team::createForm($pdo, $error, $form, $teamName, $weatherCode);
}
// Modify
elseif($modify == 1){
    echo "<h3>$title_modifyATeam</h3>\n";
    if($teamName!="") Team::modifyPopup($pdo, $teamName, $weatherCode, $teamId);
    echo Team::modifyForm($pdo, $error, $form, $teamId);
}
?>