<?php
/* This is the Football Predictions matchday section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\App;
use FootballPredictions\Language;
use FootballPredictions\Theme;
use FootballPredictions\Section\Matchday;
use FootballPredictions\Section\MatchdayForm;
use FootballPredictions\Section\MatchdayPopup;

?>
<h2><?= Theme::icon('matchday') . " "
    . (Language::title('matchday')) . " "
    . (isset($_SESSION['matchdayNum']) ? $_SESSION['matchdayNum'] : null);?></h2>
<?php
// Values
$matchdayId = $idPlayer = $ratingPlayer = $deletePlayer = 0;
$matchdayNumber = $matchdayTotalNumber = "";
if(isset($_POST['matchdayModify'])){
    $v=explode(",",$_POST['matchdayModify']);
    $matchdayId=$v[0];
}
isset($_POST['id_matchday'])   ? $matchdayId = $error->check("Digit",$_POST['id_matchday']) : null;
isset($_POST['number'])		   ? $matchdayNumber = $error->check("Digit",$_POST['number'], Language::title('number')) : null;
isset($_POST['totalNumber'])   ? $matchdayTotalNumber = $error->check("Digit",$_POST['totalNumber'], Language::title('matchdayNumber')) : null;
isset($_POST['id_player'])	   ? $idPlayer=$error->check("Digit",$_POST['id_player']) : null;
isset($_POST['rating'])		   ? $ratingPlayer=$error->check("Digit",$_POST['rating'], Language::title('rating')) : null;
$val = array();
if(
    (isset($_POST['id_player']))
    and (isset($_POST['rating']))
){
    $val=array_combine($idPlayer,$ratingPlayer);
}

// Create
if($create==1){
    if ($_SESSION['noMatchday'] == true) {
        echo "<h3>" . (Language::title('createTheMatchdays')) . "</h3>\n";
        echo MatchdayForm::createMultiForm($pdo, $error, $form);
        if($matchdayTotalNumber!="") MatchdayPopup::createMultiPopup($pdo, $matchdayTotalNumber);
    } else {
        echo "<h3>" . Language::title('createAMatchday') . "</h3>\n";
        echo MatchdayForm::createForm($pdo, $error, $form);
        if($matchdayNumber!="")      MatchdayPopup::createPopup($pdo, $matchdayNumber);
    }
}
// Delete / Modify
elseif($delete == 1  || $delete == 2 || $modify == 1){
    App::exitNoAdmin();
    echo "<h3>" . Language::title('modifyAMatchday') . "</h3>\n";
    echo MatchdayForm::modifyForm($pdo, $matchdayId, $error, $form); 
    if($delete == 1)        echo $form->popupConfirm('matchday', 'id_matchday', $matchdayId);
    elseif($delete == 2)    MatchdayPopup::deletePopup($pdo, $matchdayId);
    elseif($modify == 1){
        if($matchdayNumber != '') MatchdayPopup::modifyPopup($pdo, $matchdayNumber, $matchdayId);   
    }
}
// List
else {   
    echo "<h3>" . Language::title('listMatchdays') . "</h3>";
    echo "<h4>" . $_SESSION['championshipName']." - " 
                . $_SESSION['seasonName'] . "</h4>\n";
    echo Matchday::list($pdo, $form);
}
?>