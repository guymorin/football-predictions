<?php
/* This is the Football Predictions team section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\Errors;
use FootballPredictions\Forms;
use FootballPredictions\Section\Team;

echo "<h2>$icon_team $title_team</h2>\n";

// Values
$val=null;
isset($_POST['id_team'])&&isset($_POST['marketValue']) ? $val=array_combine($_POST['id_team'],$_POST['marketValue']) : null;

if(empty($_POST['marketValue'])){
    $teamName = "";
    $teamId = $weatherCode = 0;
    isset($_POST['name'])           ? $teamName = $error->check("Alnum",$_POST['name'], $title_name) : null;
    isset($_POST['id_team'])        ? $teamId = $error->check("Digit",$_POST['id_team']) : null;
    isset($_POST['weather_code'])   ? $weatherCode = $error->check("Digit",$_POST['weather_code'], $title_weatherCode) : null;
}

// Delete
if($delete == 1){
    echo $form->popupConfirm('team', 'id_team', $teamId);
}
elseif($delete == 2){
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
} else {
    echo "<h3>$title_marketValue</h3>";
    if(isset($val)) Team::modifyPopupMarketValue($pdo, $error, $val);
    echo Team::modifyFormMarketValue($pdo, $error, $form);
}
?>