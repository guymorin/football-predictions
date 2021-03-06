<?php
/* This is the Football Predictions match section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\App;
use FootballPredictions\Errors;
use FootballPredictions\Forms;
use FootballPredictions\Language;
use FootballPredictions\Theme;
use FootballPredictions\Section\Matchday;
use FootballPredictions\Section\Matchgame;
use FootballPredictions\Section\MatchgamePopup;
use FootballPredictions\Section\MatchgameForm;

echo "<h2>" . (Theme::icon('matchday')) . " "
        . (Language::title('matchday')) . " "
        .$_SESSION['matchdayNum']."</h2>\n";

// Values
$date = "";
$result = 'NULL';
$idMatch = $team1 = $team2 = $odds1 = $oddsD = $odds2 = 0;
isset($_POST['id_matchgame'])   ? $idMatch=$error->check("Digit",$_POST['id_matchgame']) : null;
isset($_POST['team_1'])         ? $team1=$error->check("Digit",$_POST['team_1'], Language::title('team').' 1') : null;
isset($_POST['team_2'])         ? $team2=$error->check("Digit",$_POST['team_2'], Language::title('team').' 2') : null;
isset($_POST['result'])         ? $result=$error->check("Result",$_POST['result'], Language::title('result')) : $result = 'NULL';
isset($_POST['odds1'])          ? $odds1=$error->check("Num",$_POST['odds1'], Language::title('odds').' 1') : null;
isset($_POST['oddsD'])          ? $oddsD=$error->check("Num",$_POST['oddsD'], Language::title('odds').' '.Language::title('draw')) : null;
isset($_POST['odds2'])          ? $odds2=$error->check("Num",$_POST['odds2'], Language::title('odds').' 2') : null;
isset($_POST['date'])           ? $date=$error->check("Date",$_POST['date'], Language::title('date')) : null;

// Create
if($create==1){
    echo "<h3>" . (Language::title('createAMatch')) . "</h3>\n";
    if(($team1>0)&&($team2>0)&&($team1!=$team2)) MatchgamePopup::createPopup($pdo, $team1, $team2, $result, $odds1, $oddsD, $odds2, $date);
    else                                         echo MatchgameForm::createForm($pdo, $error, $form);
}
// Delete / Modify
elseif($delete == 1  || $delete == 2 || $modify == 1){
    App::exitNoAdmin();
    echo "<h3>" . (Language::title('modifyAMatch')) . "</h3>\n";
    echo MatchgameForm::modifyForm($pdo, $error, $form, $idMatch);
    if($delete==1)       echo $form->popupConfirm('matchgame', 'id_matchgame', $idMatch);
    elseif($delete==2)   MatchgamePopup::deletePopup($pdo, $idMatch);
    elseif($modify==1){
        if(($team1>0)&&($team2>0)&&($team1!=$team2)) MatchgamePopup::modifyPopup($pdo, $team1, $team2, $result, $odds1, $oddsD, $odds2, $date, $idMatch);
    }
}
?>