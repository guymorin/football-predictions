<?php
/* This is the Football Predictions team section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\App;
use FootballPredictions\Language;
use FootballPredictions\Theme;
use FootballPredictions\Section\Team;
use FootballPredictions\Section\TeamPopup;
use FootballPredictions\Section\TeamForm;

echo "<h2>" . Theme::icon('team') . " "
        . Language::title('team')
        . "</h2>\n";

// Values
$val=null;
isset($_POST['id_team'])&&isset($_POST['marketValue']) ? $val=array_combine($_POST['id_team'],$_POST['marketValue']) : null;

if(empty($_POST['marketValue'])){
    $teamName = "";
    $teamId = $weatherCode = 0;
    isset($_POST['name'])           ? $teamName = $error->check("Alnum",$_POST['name'], Language::title('name')) : null;
    isset($_POST['id_team'])        ? $teamId = $error->check("Digit",$_POST['id_team']) : null;
    isset($_POST['weather_code'])   ? $weatherCode = $error->check("Alnum",$_POST['weather_code'], Language::title('weatherCode')) : null;
}

// Create
if($create == 1){
    echo "<h3>" . (Language::title('createATeam')) . "</h3>\n";
    if($pdo->findName('team', $teamName))  $error->setError(Language::title('errorExists'));
    elseif($teamName!="") TeamPopup::createPopup($pdo, $teamName, $weatherCode);
    echo TeamForm::createForm($pdo, $error, $form, $teamName, $weatherCode);
}
// Delete / Modify
elseif($delete == 1  || $delete == 2 || $modify == 1 || $add == 1){
    App::exitNoAdmin();
    echo "<h3>" . (Language::title('modifyATeam')) . "</h3>\n";
    echo TeamForm::modifyForm($pdo, $error, $form, $teamId);
    if($delete == 1) echo $form->popupConfirm('team', 'id_team', $teamId);
    elseif($delete == 2) TeamPopup::deletePopup($pdo, $teamId);
    elseif($modify == 1){
        if($teamName!="") TeamPopup::modifyPopup($pdo, $teamName, $weatherCode, $teamId);
    }
    elseif($add == 1){
        if($teamId!="") TeamPopup::addPopup($pdo, $teamId);
    }
} else {
    echo "<h3>" . (Language::title('marketValue')) . "</h3>";
    echo TeamForm::modifyFormMarketValue($pdo, $error, $form);
    if(isset($val)) TeamPopup::modifyPopupMarketValue($pdo, $error, $val); 
}
?>