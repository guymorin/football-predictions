<?php
/* This is the Football Predictions admin section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\Language;
use FootballPredictions\Theme;
use FootballPredictions\Section\Admin;
?>
<h2><?= Theme::icon('admin') . " "
        . Language::title('administration');?></h2>
<?php
// Values
    if(($_SESSION['role'])!=2) header('Location:index.php');
    
    // Table for administration options
    $val = "<h3>".ucfirst(Language::title('administrator'))."</h3>\n";
    $val .= "<table class='admin'>\n";
    $val .= Admin::siteData();
    $val .= Admin::accountList($pdo,$form);
    $val .= Admin::seasonList($pdo,$form);
    $val .= Admin::championshipList($pdo,$form);
    $val .= Admin::matchdayList($pdo,$form);
    $val .= Admin::teamList($pdo,$form);
    $val .= Admin::playerList($pdo,$form);
    $val .= "</table>\n";
    echo $val;
?>