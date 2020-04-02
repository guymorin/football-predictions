<?php
/* This is the Football Predictions season section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\Errors;
use FootballPredictions\Forms;

// Files to include
require 'season_nav.php';
?>

<section>
<h2><?php echo "$icon_season $title_season";?></h2>

<?php
// Values
$error = new Errors();
$form = new Forms($_POST);

$seasonId=0;
$seasonName="";
isset($_POST['id_season'])  ? $seasonId=$error->check("Digit",$_POST['id_season']) : null;
isset($_POST['name'])       ? $seasonName=$error->check("Alnum",$_POST['name']) : null;

$create=$modify=$delete=0;
isset($_GET['create'])      ? $create=$error->check("Action",$_GET['create']) : null;
$create==0&&isset($_POST['create']) ? $create=$error->check("Action",$_POST['create']) : null;
isset($_POST['modify'])     ? $modify=$error->check("Action",$_POST['modify']) : null;
isset($_POST['delete'])     ? $delete=$error->check("Action",$_POST['delete']) : null;

// First, select a season
if(
    (empty($_SESSION['seasonId']))
    &&($create==0)
    &&($modify==0)
    &&($delete==0)
){
    echo "<ul class='menu'>\n";
    $response = $db->query("SELECT id_season, name FROM season ORDER BY name;");
    
    if($response->rowCount()>0){
        // Select form
        $list = "<form action='index.php?page=championship' method='POST'>\n";
        $list.= $form->labelBr($title_selectTheSeason);
        $list.= $form->selectSubmit("seasonSelect",$response);
        $list.= "</form>\n";
        
        // Quick nav button
        $response = $db->query("SELECT id_season, name FROM season ORDER BY id_season DESC;");
        $data = $response->fetch(PDO::FETCH_OBJ);
        $form->setValues($data);
    
        echo "<form action='index.php?page=championship' method='POST'>\n";
        echo $form->inputHidden("seasonSelect",$data->id_season.",".$data->name);
        echo $form->label($title_quickNav);
        echo $form->submit($icon_quicknav." ".$data->name);
        echo "</form>\n";
        
        echo $list;
    }
    // No season
    else    echo "  <h3>$title_noSeason</h3>\n";
    $response->closeCursor();
    echo "</ul>\n";
}
/* Popup if needed */
// Delete
elseif($delete==1){
        $req="DELETE FROM season WHERE id_season=:id_season;";
        $response = $db->prepare($req);
        $response->execute([
            'id_season' => $seasonId
        ]);
        $db->exec("ALTER TABLE season AUTO_INCREMENT=0;");
        popup($title_deleted,"index.php?page=season");
}
// Create
elseif($create==1){
    echo "<h3>$title_createASeason</h3>\n";
    // Create popup
    if($seasonName!=""){
        $db->exec("ALTER TABLE season AUTO_INCREMENT=0;");
        $req="INSERT INTO season VALUES(NULL,'".$seasonName."');";
        $db->exec($req);
        popup($title_created,"index.php?page=season");
    }
    // Create form
    else {
    	echo "<form action='index.php?page=season' method='POST'>\n";
    	echo $error->getError();
    	echo $form->inputAction("create");
    	echo $form->input($title_name,"name");
    	echo $form->submit($title_create);
    	echo "</form>\n";   
	}
}
// Modify
elseif($modify==1){
    echo "<h3>$title_modifyASeason</h3>\n";
    // Modify popup
    if($seasonName!=""){
        $req="UPDATE season SET name='".$seasonName."' WHERE id_season='".$seasonId."';";
        $db->exec($req);
        popup($title_modified,"index.php?page=season");
    }
    // Modify form
    else {
        $response = $db->prepare("SELECT * FROM season 
        WHERE id_season=:id_season;");
        $response->execute([
            'id_season' => $seasonId
        ]);

        $data = $response->fetch(PDO::FETCH_OBJ);
        $form->setValues($data);
        
        echo "<form action='index.php?page=season' method='POST'>\n";
        echo $error->getError();
        echo $form->inputAction("modify");
        echo $form->inputHidden("id_season",$data->id_season); 
        echo $form->input($title_name,"name");
        echo $form->submit($title_modify);
        echo "</form>\n";
        
        // Delete form
        echo "<form action='index.php?page=season' method='POST' onsubmit='return confirm()'>\n";
        echo $form->inputAction("delete");
        echo $form->inputHidden(id_season, $seasonId);
        echo $form->inputHidden("name",$data->name); 
        echo $form->submit("&#9888 $title_delete &#9888");
        echo "</form>\n";
        
        $response->closeCursor();
    }
}
else {
    echo "<h3>".$_SESSION['seasonName']."</h3>\n";
    $response = $db->prepare("SELECT c.name, COUNT(*) as nb 
    FROM championship c
    LEFT JOIN season_championship_team scc ON c.id_championship=scc.id_championship
    WHERE scc.id_season=:id_season
    GROUP BY c.name
    ORDER BY c.name");
    $response->execute([
        'id_season' => $_SESSION['seasonId']
    ]);
    echo "<table>\n";
    echo "  <tr>\n";
    echo "      <th>$title_championship</th>\n";
    echo "      <th>$title_teams</th>\n";
    echo "  </tr>\n";
    while ($data = $response->fetch(PDO::FETCH_OBJ))
    {
        echo "  <tr>\n";
        echo "      <td>$data->name</td>\n";
        echo "      <td>$data->nb</td>\n";
        echo "  </tr>\n";
    }
    echo "</table>\n";
}
?>
</section>