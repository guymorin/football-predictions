<?php
/* This is the Football Predictions prediction section page */
/* Author : Guy Morin */

// Files to include
require("include/changeMD.php");
require("matchday_nav.php");

echo "<section>\n";
echo "<h2>$icon_matchday $title_matchday ".$_SESSION['matchdayNum']."</h2>\n";

// Values
$modify=0;
isset($_POST['modify']) ? $modify=$error->check("Action",$_POST['modify']) : null;
$expert=0;
if(isset($_POST['expert'])) $expert=$error->check("Digit",$_POST['expert']);
if(isset($_GET['expert'])) $expert=$error->check("Digit",$_GET['expert']);

/* Popups or page */
// Modified popup
if($modify==1){

    $rMatch="";
    if(isset($_POST['id_matchgame'])) $idMatch=$error->check("Digit",$_POST['id_matchgame']);
    if(isset($_POST['result'])) $rMatch=$error->check("Digit",$_POST['result']);
    if(isset($_POST['motivation1'])) $moMatch1=array_filter($_POST['motivation1']);
    if(isset($_POST['currentForm1'])) $seMatch1=array_filter($_POST['currentForm1']);
    if(isset($_POST['physicalForm1'])) $foMatch1=array_filter($_POST['physicalForm1']);
    if(isset($_POST['weather1'])) $meMatch1=array_filter($_POST['weather1']);
    if(isset($_POST['bestPlayers1'])) $joMatch1=array_filter($_POST['bestPlayers1']);
    if(isset($_POST['marketValue1'])) $vaMatch1=array_filter($_POST['marketValue1']);
    if(isset($_POST['home_away1'])) $doMatch1=array_filter($_POST['home_away1']);
    if(isset($_POST['motivation2'])) $moMatch2=array_filter($_POST['motivation2']);
    if(isset($_POST['currentForm2'])) $seMatch2=array_filter($_POST['currentForm2']);
    if(isset($_POST['physicalForm2'])) $foMatch2=array_filter($_POST['physicalForm2']);
    if(isset($_POST['weather2'])) $meMatch2=array_filter($_POST['weather2']);
    if(isset($_POST['bestPlayers2'])) $joMatch2=array_filter($_POST['bestPlayers2']);
    if(isset($_POST['marketValue2'])) $vaMatch2=array_filter($_POST['marketValue2']);
    if(isset($_POST['home_away2'])) $doMatch2=array_filter($_POST['home_away2']);
    
    $db->exec("ALTER TABLE criterion AUTO_INCREMENT=0;");
    $req="";
    
    foreach($idMatch as $k){
        if($rMatch[$k]!=""){
            $req.="UPDATE matchgame SET result='".$rMatch[$k]."' WHERE id_match='".$k."';";
        }
        $response = $db->query("SELECT COUNT(*) as nb FROM criterion WHERE id_match='".$k."';");
        $data = $response->fetch(PDO::FETCH_OBJ);
        $response->closeCursor();
        
        if($data[0]==0) {
            $req.="INSERT INTO criterion VALUES(NULL,'".$k."','";
            if(isset($moMatch1[$k])) $req.=$moMatch1[$k];else $req.=0;
            $req.="','";
            if(isset($seMatch1[$k])) $req.=$seMatch1[$k];else $req.=0;
            $req.="','";
            if(isset($foMatch1[$k])) $req.=$foMatch1[$k];else $req.=0;
            $req.="','";
            if(isset($meMatch1[$k])) $req.=$meMatch1[$k];else $req.=0;
            $req.="','";
            if(isset($joMatch1[$k])) $req.=$joMatch1[$k];else $req.=0;
            $req.="','";
            if(isset($vaMatch1[$k])) $req.=$vaMatch1[$k];else $req.=0;
            $req.="','";
            if(isset($doMatch1[$k])) $req.=$doMatch1[$k];else $req.=0;
            $req.="','";
            if(isset($moMatch2[$k])) $req.=$moMatch2[$k];else $req.=0;
            $req.="','";
            if(isset($seMatch2[$k])) $req.=$seMatch2[$k];else $req.=0;
            $req.="','";
            if(isset($foMatch2[$k])) $req.=$foMatch2[$k];else $req.=0;
            $req.="','";
            if(isset($meMatch2[$k])) $req.=$meMatch2[$k];else $req.=0;
            $req.="','";
            if(isset($joMatch2[$k])) $req.=$joMatch2[$k];else $req.=0;
            $req.="','";
            if(isset($vaMatch2[$k])) $req.=$vaMatch2[$k];else $req.=0;
            $req.="','";
            if(isset($doMatch2[$k])) $req.=$doMatch2[$k];else $req.=0;
            $req.="');";
        }
        if($data[0]==1) {
            $req.="UPDATE criterion SET ";
            $req.="motivation1='";
            if(isset($moMatch1[$k])) $req.=$moMatch1[$k];else $req.=0;
            $req.="',";
            $req.="currentForm1='";
            if(isset($seMatch1[$k])) $req.=$seMatch1[$k];else $req.=0;
            $req.="',";
            $req.="physicalForm1='";
            if(isset($foMatch1[$k])) $req.=$foMatch1[$k];else $req.=0;
            $req.="',";
            $req.="meteo1='";
            if(isset($meMatch1[$k])) $req.=$meMatch1[$k];else $req.=0;
            $req.="',";
            $req.="bestPlayers1='";
            if(isset($joMatch1[$k])) $req.=$joMatch1[$k];else $req.=0;
            $req.="',";
            $req.="marketValue1='";
            if(isset($vaMatch1[$k])) $req.=$vaMatch1[$k];else $req.=0;
            $req.="',";
            $req.="home_away1='";
            if(isset($doMatch1[$k])) $req.=$doMatch1[$k];else $req.=0;
            $req.="',";
            $req.="motivation2='";
            if(isset($moMatch2[$k])) $req.=$moMatch2[$k];else $req.=0;
            $req.="',";
            $req.="currentForm2='";
            if(isset($seMatch2[$k])) $req.=$seMatch2[$k];else $req.=0;
            $req.="',";
            $req.="physicalForm2='";
            if(isset($foMatch2[$k])) $req.=$foMatch2[$k];else $req.=0;
            $req.="',";
            $req.="meteo2='";
            if(isset($meMatch2[$k])) $req.=$meMatch2[$k];else $req.=0;
            $req.="',";
            $req.="bestPlayers2='";
            if(isset($joMatch2[$k])) $req.=$joMatch2[$k];else $req.=0;
            $req.="',";
            $req.="marketValue2='";
            if(isset($vaMatch2[$k])) $req.=$vaMatch2[$k];else $req.=0;
            $req.="',";
            $req.="home_away2='";
            if(isset($doMatch2[$k])) $req.=$doMatch2[$k];else $req.=0;
            $req.="' WHERE id_match='".$k."';";
        }  
    }
    $db->exec($req);
    popup($title_modifyPredictions,"index.php?page=prediction&expert=".$expert);

}
// Default page or expert page
else {
    
    if($expert==1) require("prediction_matchs_expert.php");
    else require("prediction_matchs_default.php");
    
}
echo "</section>\n";
?>  
