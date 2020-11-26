<?php
/* This is the Football Predictions season section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\App;
use FootballPredictions\Language;
use FootballPredictions\Theme;
use FootballPredictions\Section\Championship;
use FootballPredictions\Section\Season;
?>

<h2><?= Theme::icon('season') . " "
        . Language::title('season');?></h2>

<?php
// Values
$seasonId=0;
$seasonName="";
isset($_POST['id_season'])  ? $seasonId=$error->check("Digit",$_POST['id_season']) : null;

if(isset($_POST['name'])){
    $seasonName = $error->check("Alnum",$_POST['name'], Language::title('name'));
    if($seasonName!='') $seasonName = $error->check("Season",$_POST['name'], Language::title('name'));
}

// First, select a season
if(
    (empty($_SESSION['seasonId']))
    &&($create == 0)
    &&($modify == 0)
    &&($delete == 0)
){
    echo Season::selectSeason($pdo, $form);
}
// Create
elseif($create == 1){
    echo "<h3>" . Language::title('createASeason') . "</h3>\n";
    if($pdo->findName('season', $seasonName))  $error->addError(Language::title('name'), Language::title('errorExists'));
    elseif($seasonName!="") Season::createPopup($pdo, $seasonName);
    echo Season::createForm($error, $form);
}
// Delete / Modify
elseif($delete == 1  || $delete == 2 || $modify == 1){
    App::exitNoAdmin();
    echo "<h3>" . (Language::title('modifyASeason')) . "</h3>\n";
    echo Season::modifyForm($pdo, $error, $form, $seasonId);

    if($delete == 1) echo $form->popupConfirm('season', 'id_season', $seasonId);
    elseif($delete == 2) Season::deletePopup($pdo, $seasonId);
    elseif($modify == 1){
        if($pdo->findName('season', $seasonName))  $error->addError(Language::title('name'), Language::title('errorExists'));
        elseif($seasonName!="") Season::modifyPopup($pdo, $seasonName, $seasonId);
    }
}
else {
    echo "<h3>" . Language::title('listChampionships') . "</h3>\n";
    echo "<h4>" . $_SESSION['seasonName'] . "</h4>\n";
    echo Season::list($pdo,$form);
}
?>