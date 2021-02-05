<?php
/* This is the Football Predictions admin section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\Language;
use FootballPredictions\Theme;
use FootballPredictions\Section\Admin;
?>
<h2><?= Theme::icon('preferences') . " "
        . ucfirst('Meteo-Concept');?></h2>
<?php
// Values
    if(($_SESSION['role'])!=2) header('Location:index.php');
    
    // Table for administration options
    $val = "<h3>".ucfirst(Language::title('administrator'))."</h3>\n";

    $val .= "<form action='index.php?page=plugin_meteo_concept' method='POST'>\n";
    $val .= $form->inputAction('modify');
    $val .= "<fieldset>\n";
    $val .= "<legend>" . ucfirst('Meteo-Concept') . "</legend>\n";
    $val .= $error->getError();
    $val .= $form->input('Token', 'token');
    $val .= $form->input('URL', 'url');
    $val .= "<br />\n";
    $val .= "</fieldset>\n";
    $val .= "<fieldset>\n";
    $val .= $form->submit(Theme::icon('modify')." ".Language::title('modify'));
    $val .= "</fieldset>\n";
    $val .= "</form>\n";
    
    echo $val;
?>