<?php
/* This is the Football Predictions dashboard section page */
/* Author : Guy Morin */

use FootballPredictions\Language;
use FootballPredictions\Theme;
use FootballPredictions\Section\Championship;

echo "<h2>" . (Theme::icon('championship')) . " " . (Language::title('championship')) . "</h2>\n";

$graph=array(0=>0);

// Statistics
echo Championship::dashboard($pdo);

?>
