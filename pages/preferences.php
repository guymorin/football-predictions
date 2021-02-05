<?php
/* This is the Football Predictions preferences section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\Language;
use FootballPredictions\Section\Preferences;
use FootballPredictions\Section\PreferencesForm;
use FootballPredictions\Theme;
use FootballPredictions\Section\PreferencesPopup;

echo "<h2>";
if(isset($_SESSION['install']) and $_SESSION['install']==true) {
    echo Theme::icon('admin'). " " . Language::title('install') ." 2/3";
} else {
    echo Theme::icon('preferences'). " " . Language::title('Preferences');
}
echo "</h2>\n";

// Values
$name = '';
isset($_POST['name'])           ? $name = $error->check("Alnum",$_POST['name'],Language::title('name')) : null;
echo "<h3>" . (Language::title('Preferences')) . "</h3>\n";
if($create == 1)    echo PreferencesPopup::createPopup($name, $pdo);
elseif(isset($_SESSION['install']) and $_SESSION['install']==true){
    echo PreferencesForm::createForm($error, $form);
} else {
    if($name!=""){
        // Plugin update
        
        isset($_POST['id_plugin'])   ? $idPluginTab=$_POST['id_plugin'] : null;
        $req = '';
        foreach($idPluginTab as $k => $v){
            isset($_POST['id_plugin'])         ? $id_plugin=$_POST['id_plugin'][$v] : null;
            isset($_POST['activate'])          ? $activate =$_POST['activate'][$v] : $activate = 0;
            $req .= "UPDATE plugin 
                SET activate='" . $activate . "'"
                . " WHERE id_plugin='" . $id_plugin . "';";
        }
        $pdo->exec($req);
        // Other updates and popup
        echo PreferencesPopup::modifyPopup($name, $pdo);
    } else {
       echo PreferencesForm::modifyForm($pdo, $error, $form);
    }
}
?>