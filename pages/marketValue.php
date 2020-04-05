<?php
/* This is the Football Predictions marketvalue section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\Errors;
use FootballPredictions\Forms;

?>

<h2><?= "$icon_team $title_team";?></h2>
<h3><?= $title_marketValue;?></h3>

<?php
// Values
$teamId=$valClub=0;
isset($_POST['id_team'])        ? $teamId=$error->check("Digit",$_POST['id_team']) : null;
isset($_POST['marketValue'])    ? $valClub=$error->check("Digit",$_POST['marketValue']) : null;
$val=array_combine($teamId,$valClub);

// Modify popup
if(isset($val)){
    $db->exec("ALTER TABLE marketValue AUTO_INCREMENT=0;");
    $req="";
    foreach($val as $k=>$v){
        $v=$error->check("Digit",$v);
        if($v>0){     
            $response = $db->prepare("SELECT COUNT(*) as nb FROM marketValue 
            WHERE id_season=:id_season AND id_team=:id_team;");
            $response->execute([
                'id_season' => $_SESSION['seasonId'],
                'id_team' => $k
            ]); 
            $data = $response->fetch(PDO::FETCH_OBJ);
            $response->closeCursor();
            echo $data[0];
            if($data[0]==0){
                $req.="INSERT INTO marketValue VALUES(NULL,'".$_SESSION['seasonId']."','".$k."','".$v."');";
            }
            if($data[0]==1){
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
    WHERE scc.id_season=:id_season 
    AND scc. id_championship=:id_championship;";
    $response = $db->prepare($req);
    $response->execute([
        'id_season' => $_SESSION['seasonId'],
        'id_championship' => $_SESSION['championshipId']
    ]);
    echo "<form action='index.php?page=marketValue' method='POST'>\n";
    echo $error->getError();
    echo $form->label($title_modifyAMarketValue);
    echo "<table>\n";
    echo "  <tr>\n";
    echo "      <th>Club</th>\n";
    echo "      <th>$title_marketValue (M €)</th>\n";
    echo "  </tr>\n";
        while ($data = $response->fetch(PDO::FETCH_OBJ))
        {
            echo "  <tr>\n";
            echo "      <td>\n";
            echo $form->inputHidden('id_team[]', $data->id_team);
            echo $data->name;
            echo "      </td>\n";
            echo "      <td>\n";
            $form->setValue('marketValue[]',$data->marketValue);
            echo $form->input('', 'marketValue[]');
            echo "      </td>\n";
            echo "  </tr>\n";
        }
    echo "</table>\n";
    echo $form->submit($title_modify);
    $response->closeCursor();  
}

?>
