<?php
/* This is the Football Predictions install section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\Language;
use FootballPredictions\Section\Install;


echo "<h2>" . (Language::title('install')) . "</h2>\n";

// Values
$test = $DbHost = $DbName = $DbUser = $DbPass = '';
isset($_POST['DbHost'])           ? $DbHost = $error->check("Alnum",$_POST['DbHost'],Language::title('installHost')) : null;
isset($_POST['DbName'])           ? $DbName = $error->check("Alnum",$_POST['DbName'],Language::title('installName')) : null;
isset($_POST['DbUser'])           ? $DbUser = $error->check("Alnum",$_POST['DbUser'],Language::title('installUser')) : null;
isset($_POST['DbPass'])           ? $DbPass = $error->check("Alnum",$_POST['DbPass'],Language::title('installPass')) : null;

if($DbHost!='' && $DbName!='' && $DbUser!='' && $DbPass!=''){
    $test = $error->checkDb($DbHost, $DbName, $DbUser, $DbPass);
}

echo "<h3>" . (Language::title('createDatabase')) . "</h3>\n";
if($test){
    echo Install::createPopup($DbHost, $DbName, $DbUser, $DbPass);
}
echo Install::createForm($error, $form);

?>
