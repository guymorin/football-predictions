<?php
/* This is the Football Predictions matchday section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\Errors;
use FootballPredictions\Forms;
use FootballPredictions\Language;
use FootballPredictions\Section\Matchday;
use FootballPredictions\Section\Championship;

// Files to include
require '../include/changeMD.php';
?>

<h2><?= $icon_matchday . ' ' . (Language::title('matchday')) . ' ' . (isset($_SESSION['matchdayNum']) ? $_SESSION['matchdayNum'] : null);?></h2>

<?php
// Values
$matchdayId=0;
$matchdayNumber="";

if(isset($_POST['matchdaySelect'])){
    $v=explode(",",$_POST['matchdaySelect']);
    $matchdayId=$v[0];
}

isset($_POST['id_matchday'])   ? $matchdayId=$error->check("Digit",$_POST['id_matchday']) : null;
isset($_POST['number'])		   ? $matchdayNumber=$error->check("Digit",$_POST['number'], Language::title('number')) : null;

$idPlayer = $ratingPlayer = $deletePlayer = 0;
isset($_POST['id_player'])	   ? $idPlayer=$error->check("Digit",$_POST['id_player']) : null;
isset($_POST['rating'])		   ? $ratingPlayer=$error->check("Digit",$_POST['rating'], Language::title('rating')) : null;

$val=array_combine($idPlayer,$ratingPlayer);

// Delete
if($delete==1){
    echo $form->popupConfirm('matchday', 'id_matchday', $matchdayId);
}
elseif($delete==2){
    Matchday::deletePopup($pdo, $matchdayId);
}
// Create
elseif($create==1){
    echo "<h3>" . (Language::title('createAMatchday')) . "</h3>\n";
    if($matchdayNumber!="") Matchday::createPopup($pdo, $matchdayNumber);
    else echo Matchday::createForm($pdo, $error, $form);
}
// Modify
elseif($modify==1){
    echo "<h3>" . (Language::title('modifyAMatchday')) . "</h3>\n";
    if($matchdayNumber!="") Matchday::modifyPopup($pdo, $matchdayNumber, $matchdayId);
    else  echo Matchday::modifyForm($pdo, $data, $matchdayId, $error, $form);        
}
// List
else {
    echo Matchday::list($pdo);
}
?>