<?php
/* This is the Football Predictions marketvalue section page */
/* Author : Guy Morin */

// Files to include
include("team_nav.php");

echo "<section>\n";
echo "<h2>$icon_team $title_team</h2>\n";

// Values
$teamId=0;
$valClub=0;
if(isset($_POST['id_team'])) $teamId=$_POST['id_team'];
if(isset($_POST['marketValue'])) $valClub=$_POST['marketValue'];
$val=array_combine($teamId,$valClub);
$modify=1;
if((isset($_POST['modify']))&&($_POST['modify']==1)) $modify=$_POST['modify'];

// Modify
if($modify==1){
    echo "<h3>$title_marketValue</h3>\n";
    // Modify popup
    if(isset($val)) {
        $db->exec("ALTER TABLE marketValue AUTO_INCREMENT=0;");
        $req="";
        foreach($val as $k=>$v){
            if($v>0){     
                $response = $db->query("SELECT COUNT(*) as nb FROM marketValue WHERE id_season='".$_SESSION['seasonId']."' AND id_team='".$k."';");
                $data = $response->fetch();
                $response->closeCursor();
                echo $data[0];
                if($data[0]==0) {
                    $req.="INSERT INTO marketValue VALUES(NULL,'".$_SESSION['seasonId']."','".$k."','".$v."');";
                }
                if($data[0]==1) {
                    $req.="UPDATE marketValue SET marketValue='".$v."' WHERE id_season='".$_SESSION['seasonId']."' AND id_team='".$k."';";
                }
            }
        } 
        $db->exec($req);
        popup($title_modified,"index.php?page=marketValue");
    }
    // Modify form
    else {
        $req = "SELECT c.*, v.marketValue 
        FROM team c 
        LEFT JOIN marketValue v ON v.id_team=c.id_team
        LEFT JOIN season_championship_team scc ON scc.id_team=c.id_team 
        WHERE scc.id_season='".$_SESSION['seasonId']."' 
        AND scc. id_championship='".$_SESSION['championshipId']."';";
        $response = $db->query($req);
        echo "	 <form action='index.php?page=marketValue' method='POST'>\n";
        echo "      <label>$title_modifyAMarketValue :</label>\n";  
        echo "      <table>\n";
        echo "          <tr><th>Club</th><th>$title_marketValue (M €)</th></tr>\n";
            while ($data = $response->fetch())
            {
                echo "          <tr><td><input type='hidden' name='id_team[]' readonly  value='".$data['id_team']."'>".$data['name']."</td><td><input type='text' name='marketValue[]' value='".$data['marketValue']."'></td></tr>\n";
            }
        echo "  </table>\n";
        echo "      <input type='submit'>\n";
        $response->closeCursor();  
    }
}
echo "</section>\n";
?>
