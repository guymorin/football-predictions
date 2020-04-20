<?php
/* This is the Football Predictions player section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\App;
use FootballPredictions\Errors;
use FootballPredictions\Forms;
use FootballPredictions\Language;
use FootballPredictions\Theme;
use FootballPredictions\Section\Player;

echo "<h2>" . Theme::icon('player') . " " . (Language::title('player')) . "</h2>\n";

// Values
$playerId = $teamId = 0;
$playerName = $playerFirstname = $playerPosition = '';
isset($_POST['id_player'])      ? $playerId = $error->check("Digit",$_POST['id_player']) : null;
isset($_POST['name'])           ? $playerName = $error->check("Alnum",$_POST['name'], Language::title('name')) : null;
isset($_POST['firstname'])      ? $playerFirstname = $error->check("Alnum",$_POST['firstname'], Language::title('firstname')) : null;
isset($_POST['position'])       ? $playerPosition = $error->check("Position",$_POST['position'], Language::title('position')) : null;
isset($_POST['id_team'])        ? $teamId = $error->check("Digit",$_POST['id_team']) : null;


// Create
if($create==1){
    echo "<h3>" . (Language::title('createAPlayer')) . "</h3>\n";
    if(($playerName!="")&&($playerFirstname==$_POST['firstname'])&&($playerPosition!="")&&($teamId>0)){
        Player::createPopup($pdo, $teamId, $playerId, $playerName, $playerFirstname, $playerPosition);
    }
    echo Player::createForm($pdo, $error, $form);
}
// Delete / Modify
elseif($delete == 1  || $delete == 2 || $modify == 1){
    App::exitNoAdmin();
    echo "<h3>" . (Language::title('modifyAPlayer')) . "</h3>\n";
    echo Player::modifyForm($pdo, $error, $form, $playerId);
    if($delete == 1) echo $form->popupConfirm('player', 'id_player', $playerId, 'id_team', $teamId);
    elseif($delete==2) Player::deletePopup($pdo, $teamId, $playerId);
    elseif($modify==1){
        if(($playerName!="")&&($playerFirstname==$_POST['firstname'])&&($playerPosition!="")&&($teamId>0)){
            Player::modifyPopup($pdo, $teamId, $playerId, $playerName, $playerFirstname, $playerPosition);
        } 
    }
}

// List (best players)
else {
    echo "<h3>" . (Language::title('bestPlayers')) . "</h3>\n";
    echo Player::list($pdo);  
}
?>
