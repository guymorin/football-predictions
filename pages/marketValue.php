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
$val=null;
isset($_POST['id_team'])&&isset($_POST['marketValue']) ? $val=array_combine($_POST['id_team'],$_POST['marketValue']) : null;

// Modify popup
if(isset($val)){
    $pdo->alterAuto('marketValue');
    $req="";
    foreach($val as $k=>$v){
        $v=$error->check("Digit",$v);
        if($v>0){
            $r = "SELECT COUNT(*) as nb FROM marketValue
            WHERE id_season=:id_season AND id_team=:id_team;";
            $data = $pdo->prepare($r,[
                'id_season' => $_SESSION['seasonId'],
                'id_team' => $k
            ]);

            if($data->nb==0){
                $req .= "INSERT INTO marketValue VALUES(NULL,'".$_SESSION['seasonId']."','".$k."','".$v."');";
            }
            if($data->nb==1){
                $req .= "UPDATE marketValue SET marketValue='".$v."' WHERE id_season='".$_SESSION['seasonId']."' AND id_team='".$k."';";
            }
        }
    } 
    $pdo->exec($req);
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
    $data = $pdo->prepare($req,[
        'id_season' => $_SESSION['seasonId'],
        'id_championship' => $_SESSION['championshipId']
    ],true);
    echo $error->getError();
    echo "<form action='index.php?page=marketValue' method='POST'>\n";
    $form->setValues($data);
    echo $form->label($title_modifyAMarketValue);
    echo "<table>\n";
    echo "  <tr>\n";
    echo "      <th>$title_team</th>\n";
    echo "      <th>$title_marketValue</th>\n";
    echo "  </tr>\n";
        foreach ($data as $d)
        {
            echo "  <tr>\n";
            echo "      <td>\n";
            echo $form->inputHidden('id_team[]', $d->id_team);
            echo $d->name;
            echo "      </td>\n";
            echo "      <td>\n";
            $form->setValue('marketValue[]',$d->marketValue);
            echo $form->input('', 'marketValue[]');
            echo "      </td>\n";
            echo "  </tr>\n";
        }
    echo "</table>\n";
    echo $form->submit($title_modify);
      
}

?>
