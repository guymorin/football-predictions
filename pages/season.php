<?php
/* This is the Football Predictions season section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\Errors;
use FootballPredictions\Forms;
use FootballPredictions\Language;
use FootballPredictions\Section\Season;

?>

<h2><?= $icon_season . " " . (Language::title('season'));?></h2>

<?php
// Values
$seasonId=0;
$seasonName="";
isset($_POST['id_season'])  ? $seasonId=$error->check("Digit",$_POST['id_season']) : null;
isset($_POST['name'])       ? $seasonName=$error->check("Alnum",$_POST['name'], Language::title('name')) : null;

// First, select a season
if(
    (empty($_SESSION['seasonId']))
    &&($create == 0)
    &&($modify == 0)
    &&($delete == 0)
){
    echo Season::selectSeason($pdo, $form, $icon_quicknav);
}
// Delete
elseif($delete == 1){
    echo $form->popupConfirm('season', 'id_season', $seasonId);
}
elseif($delete == 2){
    Season::deletePopup($pdo, $seasonId);
}
// Create
elseif($create == 1){
    echo "<h3>" . (Language::title('createASeason')) . "</h3>\n";
    if($pdo->findName('season', $seasonName))  $error->addError(Language::title('name'), Language::title('errorExists'));
    elseif($seasonName!="") Season::createPopup($pdo, $seasonName);
    echo Season::createForm($error, $form);
}
// Modify
elseif($modify == 1){
    echo "<h3>" . (Language::title('modifyASeason')) . "</h3>\n";
    if($pdo->findName('season', $seasonName))  $error->addError(Language::title('name'), Language::title('errorExists'));
    elseif($seasonName!="") Season::modifyPopup($pdo, $seasonName, $seasonId);
    echo Season::modifyForm($pdo, $error, $form, $seasonId);
}
// List
else {
    echo "<h3>" . (Language::title('listChampionships')) . " ".$_SESSION['seasonName']."</h3>\n";
    echo Season::list($pdo);
}
?>
</section>