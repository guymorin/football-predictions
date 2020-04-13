<?php
/* This is the Football Predictions prediction section page */
/* Author : Guy Morin */

// Files to include
require '../include/changeMD.php';

echo "<h2>$icon_matchday $title_matchday ".$_SESSION['matchdayNum']."</h2>\n";

// Values
$expert = 0;
isset($_POST['expert'])     ? $expert=$error->check("Digit",$_POST['expert']) : null;
if($expert==0) isset($_GET['expert'])      ? $expert=$error->check("Digit",$_GET['expert']) : null;

// Modified popup
if($modify==1){

    $rMatch="";
    isset($_POST['id_matchgame'])   ? $idMatch = $error->check("Digit",$_POST['id_matchgame']) : null;
    isset($_POST['result'])         ? $rMatch = $error->check("Digit",$_POST['result'], $title_result) : null;
    isset($_POST['motivation1'])    ? $moMatch1 = array_filter($_POST['motivation1']) : null;
    isset($_POST['currentForm1'])   ? $seMatch1 = array_filter($_POST['currentForm1']) : null;
    isset($_POST['physicalForm1'])  ? $foMatch1 = array_filter($_POST['physicalForm1']) : null;
    isset($_POST['weather1'])       ? $meMatch1 = array_filter($_POST['weather1']) : null;
    isset($_POST['bestPlayers1'])   ? $joMatch1 = array_filter($_POST['bestPlayers1']) : null;
    isset($_POST['marketValue1'])   ? $vaMatch1 = array_filter($_POST['marketValue1']) : null;
    isset($_POST['home_away1'])     ? $doMatch1 = array_filter($_POST['home_away1']) : null;
    isset($_POST['motivation2'])    ? $moMatch2 = array_filter($_POST['motivation2']) : null;
    isset($_POST['currentForm2'])   ? $seMatch2 = array_filter($_POST['currentForm2']) : null;
    isset($_POST['physicalForm2'])  ? $foMatch2 = array_filter($_POST['physicalForm2']) : null;
    isset($_POST['weather2'])       ? $meMatch2 = array_filter($_POST['weather2']) : null;
    isset($_POST['bestPlayers2'])   ? $joMatch2 = array_filter($_POST['bestPlayers2']) : null;
    isset($_POST['marketValue2'])   ? $vaMatch2 = array_filter($_POST['marketValue2']) : null;
    isset($_POST['home_away2'])     ? $doMatch2 = array_filter($_POST['home_away2']) : null;
    
    $pdo->alterAuto('criterion');
    $req="";
    
    foreach($idMatch as $k){
        if($rMatch[$k]!=""){
            $req.="UPDATE matchgame SET result='".$rMatch[$k]."' WHERE id_match='".$k."';";
        }
        $data = $pdo->prepare("SELECT COUNT(*) as nb FROM criterion 
        WHERE id_match=:id_match;",[
            'id_match' => $k
        ]);
        
        if($data->nb == 0){
            $req.="INSERT INTO criterion VALUES(NULL,'".$k."','";
            isset($moMatch1[$k]) ? $req.=$moMatch1[$k] : $req.=0;
            $req.="','";
            isset($seMatch1[$k]) ? $req.=$seMatch1[$k] : $req.=0;
            $req.="','";
            isset($foMatch1[$k]) ? $req.=$foMatch1[$k] : $req.=0;
            $req.="','";
            isset($meMatch1[$k]) ? $req.=$meMatch1[$k] : $req.=0;
            $req.="','";
            isset($joMatch1[$k]) ? $req.=$joMatch1[$k] : $req.=0;
            $req.="','";
            isset($vaMatch1[$k]) ? $req.=$vaMatch1[$k] : $req.=0;
            $req.="','";
            isset($doMatch1[$k]) ? $req.=$doMatch1[$k] : $req.=0;
            $req.="','";
            isset($moMatch2[$k]) ? $req.=$moMatch2[$k] : $req.=0;
            $req.="','";
            isset($seMatch2[$k]) ? $req.=$seMatch2[$k] : $req.=0;
            $req.="','";
            isset($foMatch2[$k]) ? $req.=$foMatch2[$k] : $req.=0;
            $req.="','";
            isset($meMatch2[$k]) ? $req.=$meMatch2[$k] : $req.=0;
            $req.="','";
            isset($joMatch2[$k]) ? $req.=$joMatch2[$k] : $req.=0;
            $req.="','";
            isset($vaMatch2[$k]) ? $req.=$vaMatch2[$k] : $req.=0;
            $req.="','";
            isset($doMatch2[$k]) ? $req.=$doMatch2[$k] : $req.=0;
            $req.="');";
        }
        if($data->nb == 1){
            $req.="UPDATE criterion SET ";
            $req.="motivation1='";
            isset($moMatch1[$k]) ? $req.=$moMatch1[$k] : $req.=0;
            $req.="',";
            $req.="currentForm1='";
            isset($seMatch1[$k]) ? $req.=$seMatch1[$k] : $req.=0;
            $req.="',";
            $req.="physicalForm1='";
            isset($foMatch1[$k]) ? $req.=$foMatch1[$k] : $req.=0;
            $req.="',";
            $req.="meteo1='";
            isset($meMatch1[$k]) ? $req.=$meMatch1[$k] : $req.=0;
            $req.="',";
            $req.="bestPlayers1='";
            isset($joMatch1[$k]) ? $req.=$joMatch1[$k] : $req.=0;
            $req.="',";
            $req.="marketValue1='";
            isset($vaMatch1[$k]) ? $req.=$vaMatch1[$k] : $req.=0;
            $req.="',";
            $req.="home_away1='";
            isset($doMatch1[$k]) ? $req.=$doMatch1[$k] : $req.=0;
            $req.="',";
            $req.="motivation2='";
            isset($moMatch2[$k]) ? $req.=$moMatch2[$k] : $req.=0;
            $req.="',";
            $req.="currentForm2='";
            isset($seMatch2[$k]) ? $req.=$seMatch2[$k] : $req.=0;
            $req.="',";
            $req.="physicalForm2='";
            isset($foMatch2[$k]) ? $req.=$foMatch2[$k] : $req.=0;
            $req.="',";
            $req.="meteo2='";
            isset($meMatch2[$k]) ? $req.=$meMatch2[$k] : $req.=0;
            $req.="',";
            $req.="bestPlayers2='";
            isset($joMatch2[$k]) ? $req.=$joMatch2[$k] : $req.=0;
            $req.="',";
            $req.="marketValue2='";
            isset($vaMatch2[$k]) ? $req.=$vaMatch2[$k] : $req.=0;
            $req.="',";
            $req.="home_away2='";
            isset($doMatch2[$k]) ? $req.=$doMatch2[$k] : $req.=0;
            $req.="' WHERE id_match='".$k."';";
        }  
    }
    $pdo->exec($req);
    popup($title_modifyPredictions,"index.php?page=prediction&expert=".$expert);

}
// Default page or expert page
else {
    
    if($expert==1) require 'prediction_matchs_expert.php';
    else require 'prediction_matchs_default.php';
    
}
?>  
