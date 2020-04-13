<?php
/* This is the Football Predictions match section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\Errors;
use FootballPredictions\Forms;
use FootballPredictions\Section\Matchday;


echo "<h2>$icon_matchday $title_matchday ".$_SESSION['matchdayNum']."</h2>\n";

// Values
$date = $result = "";
$idMatch = $team1 = $team2 = $odds1 = $oddsD = $odds2 = 0;
isset($_POST['id_match'])       ? $idMatch=$error->check("Digit",$_POST['id_match']) : null;
isset($_POST['team_1'])         ? $team1=$error->check("Digit",$_POST['team_1'], $title_team.' 1') : null;
isset($_POST['team_2'])         ? $team2=$error->check("Digit",$_POST['team_2'], $title_team.' 2') : null;
isset($_POST['result'])         ? $result=$error->check("Result",$_POST['result'], $title_result) : null;
isset($_POST['odds1'])          ? $odds1=$error->check("Digit",$_POST['odds1'], $title_odds.' 1') : null;
isset($_POST['oddsD'])          ? $oddsD=$error->check("Digit",$_POST['oddsD'], $title_odds.' '.$title_draw) : null;
isset($_POST['odds2'])          ? $odds2=$error->check("Digit",$_POST['odds2'], $title_odds.' 2') : null;
isset($_POST['date'])           ? $date=$error->check("Date",$_POST['date'], $title_date) : null;

// Delete
if($delete==1){
    echo $form->popupConfirm('match', 'id_match', $idMatch);
}
elseif($delete==2){
    Matchday::deletePopupMatch($pdo, $idMatch);
}
// Create
elseif($create==1){
    echo "<h3>$title_createAMatch</h3>\n";

    if(($team1>0)&&($team2>0)&&($team1!=$team2)) Matchday::createPopupMatch($pdo, $team1, $team2, $result, $odds1, $oddsD, $odds2, $date);
    else echo Matchday::createMatchForm($pdo, $error, $form);
}
// Modify
else {
    echo "<h3>$title_modifyAMatch</h3>\n";
    if(($team1>0)&&($team2>0)&&($team1!=$team2)) Matchday::modifyPopupMatch($pdo, $team1, $team2, $result, $idMatch);
    elseif($idMatch>0) echo Matchday::modifyFormMatch($pdo, $error, $form, $idMatch);
}
?>  
