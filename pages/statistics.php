<?php
/* This is the Football Predictions statistic section page */
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
if(isset($_SESSION['matchdayId'])){
        echo Matchday::stats($pdo);
}
?>