<?php
/* This is the Football Predictions admin section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\Language;
use FootballPredictions\Theme;
use FootballPredictions\Plugin\MeteoConceptForm;
use FootballPredictions\Plugin\MeteoConceptPopup;
?>
<h2><?= Theme::icon('preferences') . " "
        . ucfirst('Meteo-Concept');?></h2>
<?php
// Values
    if(($_SESSION['role'])!=2) header('Location:index.php');
    
    $url = $token = "";
    isset($_POST['url'])    ? $url = $error->check("Alnum",$_POST['url']) : null;
    isset($_POST['token'])  ? $token = $error->check("Alnum",$_POST['token']) : null;
    
    
    // Table for administration options
    echo "<h3>".ucfirst(Language::title('administrator'))."</h3>\n";

    echo MeteoConceptForm::modifyForm($pdo, $error, $form);
    if($modify == 1) MeteoConceptPopup::modifyPopup($pdo, $url, $token);
    
?>