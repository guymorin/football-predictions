<?php
/* This is the Football Predictions dashboard section page */
/* Author : Guy Morin */

use FootballPredictions\Language;
use FootballPredictions\Theme;
use FootballPredictions\Section\Championship;

$titlePage = '';
if(isset($_SESSION['championshipId']) && $_SESSION['championshipId']>0) {
    $titlePage = $_SESSION['championshipName'];
} else {
    $titlePage = Language::title('championship');
}

echo "<h2>" . (Theme::icon('championship')) . " " . $titlePage . "</h2>\n";

$graph=array(0=>0);

// Statistics
echo Championship::dashboard($pdo);

?>
