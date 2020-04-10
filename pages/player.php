<?php
/* This is the Football Predictions player section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\Errors;
use FootballPredictions\Forms;
use FootballPredictions\Section\Player;

echo "<h2>$icon_player $title_player</h2>\n";

// Values
$playerId = $teamId = 0;
$playerName = $playerFirstname = $playerPosition = "";
isset($_POST['id_player'])      ? $playerId = $error->check("Digit",$_POST['id_player']) : null;
isset($_POST['name'])           ? $playerName = $error->check("Alnum",$_POST['name']) : null;
isset($_POST['firstname'])      ? $playerFirstname = $error->check("Alnum",$_POST['firstname']) : null;
isset($_POST['position'])       ? $playerPosition = $error->check("Position",$_POST['position']) : null;
isset($_POST['id_team'])        ? $teamId = $error->check("Digit",$_POST['id_team']) : null;
$create=$modify=$delete=0;
isset($_GET['create'])          ? $create = $error->check("Action",$_GET['create']) : null;
$create==0 && isset($_POST['create'])         ? $create = $error->check("Action",$_POST['create']) : null;
isset($_GET['modify'])          ? $modify = $error->check("Action",$_GET['modify']) : null;
isset($_POST['modify'])         ? $modify = $error->check("Action",$_POST['modify']) : null;
isset($_POST['delete'])         ? $delete = $error->check("Action",$_POST['delete']) : null;

// Delete
if($delete==1){
    Player::deletePopup($pdo, $teamId, $playerId);
}
// Create
elseif($create==1){
    echo "<h3>$title_createAPlayer</h3>\n";
    if(($playerName!="")&&($playerFirstname==$_POST['firstname'])&&($playerPosition!="")&&($teamId>0)){
        Player::createPopup($pdo, $teamId, $playerId, $playerName, $playerFirstname, $playerPosition);
    }
    else  echo Player::createForm($pdo, $error, $form);
}
// Modify
elseif($modify==1){
    echo "<h3>$title_modifyAPlayer</h3>\n";
    if(($playerName!="")&&($playerFirstname==$_POST['firstname'])&&($playerPosition!="")&&($teamId>0)){
        Player::modifyPopup($pdo, $teamId, $playerId, $playerName, $playerFirstname, $playerPosition);
    } elseif($playerId!=0) echo Player::modifyForm($pdo, $error, $form, $playerId);
}
// List (best players)
else {
    echo "<h3>$title_bestPlayers</h3>\n";
    echo Player::list($pdo);  
}
?>
