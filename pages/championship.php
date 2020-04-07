<?php
/* This is the Football Predictions championship section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\Errors;
use FootballPredictions\Forms;

?>

<h2><?= "$icon_championship $title_championship";?></h2>

<?php
// Values

$championshipId=$create=$modify=$delete=$standaway=$standhome=0;
$championshipName="";
isset($_POST['id_championship'])    ? $championshipId=$error->check("Digit",$_POST['id_championship']) : null;
isset($_POST['name'])		        ? $championshipName=$error->check("Alnum",$_POST['name'],$error) : null;

isset($_GET['create'])              ? $create=$error->check("Action",$_GET['create']) : null;
$create==0&&isset($_POST['create']) ? $create=$error->check("Action",$_POST['create']) : null;
isset($_POST['modify'])             ? $modify=$error->check("Action",$_POST['modify']) : null;
isset($_POST['delete'])             ? $delete=$error->check("Action",$_POST['delete']) : null;
isset($_GET['standhome'])           ? $standhome=$error->check("Action",$_GET['standhome']) : null;
isset($_GET['standaway'])           ? $standaway=$error->check("Action",$_GET['standaway']) : null;

// First, select a championship
if(
    (empty($_SESSION['championshipId']))
    &&($create==0)
    &&($modify==0)
    &&($delete==0)
    ){
    echo "<ul class='menu'>\n";
    $req = "SELECT DISTINCT c.id_championship, c.name 
    FROM championship c 
    ORDER BY c.name;";
    $counter = $pdo->rowCount($req);
    $list="";
    
    if($counter>0){

        // Select form
        $list.="<form action='index.php' method='POST'>\n";
        $list.= $form->labelBr($title_selectTheChampionship);
        $response = $pdo->query($req);
        $list.= $form->selectSubmit("championshipSelect", $response);
        $list.="</form>\n";
        
        // Quick nav button
        $req = "SELECT DISTINCT sct.id_championship, c.name
        FROM season_championship_team sct
        LEFT JOIN championship c ON c.id_championship = sct.id_championship
        ORDER BY c.name DESC;";
        $data = $pdo->queryObj($req);

        echo "<form action='index.php' method='POST'>\n";
        echo $form->labelBr($title_quickNav);
        echo $form->inputHidden("championshipSelect",$data->id_championship.",".$data->name);
        echo $form->submit($icon_quicknav." ".$data->name);
        echo "</form>\n";
        
        echo $list;
    }
    // No championship
    else    echo "  <h2>$title_noChampionship</h2>\n";
    $response->closeCursor();
    echo "</ul>\n";
}
/* Popups or page */
// Deleted popup
elseif($delete==1){
    if($championshipId==0){
        popup($title_error,"index.php?page=championship");
    } else {
        $req="DELETE FROM championship WHERE id_championship=:id_championship;";
        $pdo->prepare($req,[
            'id_championship' => $championshipId
        ]);
        $pdo->alterAuto('championship');
        popup($title_deleted,"index.php?page=championship");
    }
}
// Created popup or create form
elseif($create==1){
    echo "<h3>$title_createAChampionship</h3>\n";
    
    if($pdo->findName('championship', $championshipName))  $error->setError($title_errorExists);
    // Create popup
    elseif($championshipName!=""){
        $pdo->alterAuto('championship');
        $req="INSERT INTO championship VALUES(NULL,'".$championshipName."');";
        $pdo->exec($req);
        popup($title_created,"index.php?page=championship");
    }
    // Created form
    else {
        echo $error->getError();
        echo "<form action='index.php?page=championship' method='POST'>\n";
        echo $form->inputAction('create');
        echo $form->input($title_name,"name");
        echo $form->submit($title_create);
        echo "</form>\n";   
    }
}
// Modified popup or modify form
elseif($modify==1){
    echo "<h3>$title_modifyAChampionship</h3>\n";
    if($championshipId==0){
        popup($title_error,"index.php?page=championship");
    }
    // Modify popup
    elseif($championshipName!="") {
        $req="UPDATE championship
        SET name=:name 
        WHERE id_championship=:id_championship;";
        $pdo->prepare($req,[
            'name' => $championshipName,
            'id_championship' => $championshipId
        ]);
        popup($title_modified,"index.php?page=championship");
    }
    // Modify form
    else {
        $req = "SELECT * FROM championship WHERE id_championship=:id_championship;";        
        $data = $pdo->prepare($req,[
            'id_championship' => $championshipId
        ]);
        echo $error->getError();
        echo "<form action='index.php?page=championship' method='POST'>\n";
        $form->setValues($data);
        echo $form->inputAction('modify');
        echo $form->inputHidden("id_championship", $data->id_championship);
        echo $form->input($title_name, "name");
        echo $form->submit($title_modify);
        echo "</form>\n";
        
        echo "<form action='index.php?page=championship' method='POST' onsubmit='return confirm()'>\n";
        echo $form->inputAction('delete');
        echo $form->inputHidden("id_championship", $championshipId);
        echo $form->inputHidden("name", $data->name);
        echo $form->submit("&#9888 $title_delete ".$data->name." &#9888");
        echo "</form>\n";
        $response->closeCursor();
    }
}
// Default page
elseif(isset($_SESSION['championshipId'])&&($exit==0)){
    echo "<h3>".$_SESSION['championshipName']." : $title_standing</h3>\n";
    echo "<div id='standing'>\n";
    echo "<ul>\n";
    echo "  <li>";
    if($standhome+$standaway==0) echo "<p>$title_general</p>";
    else echo "<a href='index.php?page=championship'>$title_general</a>";
    echo "  </li>\n\t<li>";
    if($standhome==1) echo "<p>$title_home</p>";
    else echo "<a href='index.php?page=championship&standhome=1'>$title_home</a>";
    echo "  </li>\n\t<li>";
    if($standaway==1) echo "<p>$title_away</p>";
    else echo "<a href='index.php?page=championship&standaway=1'>$title_away</a>\n";
    echo "  </li>\n";
    echo "</ul>\n";
    
    echo "    <table>\n";
    echo "      <tr>\n";
    echo "            <th> </th>\n";
    echo "            <th>$title_team</th>\n";
    echo "            <th>$title_pts</th>\n";
    echo "            <th>$title_MD</th>\n";
    echo "            <th>$title_win</th>\n";
    echo "            <th>$title_draw</th>\n";
    echo "            <th>$title_lose</th>\n";
    echo "      </tr>\n";
    
    $req="SELECT c.id_team, c.name, COUNT(m.id_matchgame) as matchgame,
    	SUM(";
    if($standaway==0){
            $req.="CASE WHEN m.result = '1' AND m.team_1=c.id_team THEN 3 ELSE 0 END +
    		CASE WHEN m.result = 'D' AND m.team_1=c.id_team THEN 1 ELSE 0 END +";
    }
    if($standhome==0){
    		$req.="CASE WHEN m.result = '2' AND m.team_2=c.id_team THEN 3 ELSE 0 END +
    		CASE WHEN m.result = 'D' AND m.team_2=c.id_team THEN 1 ELSE 0 END +";
    }
    $req.="0) as points,";
    $req.="	SUM(";
    if($standaway==0){
            $req.="CASE WHEN m.result = '1' AND m.team_1=c.id_team THEN 1 ELSE 0 END +
            ";
    }
    if($standhome==0){
        	$req.="CASE WHEN m.result = '2' AND m.team_2=c.id_team THEN 1 ELSE 0 END +";
    }
    $req.="0) as gagne,";
    $req.="SUM(";
    if($standaway==0){
    		$req.="CASE WHEN m.result = 'D' AND m.team_1=c.id_team THEN 1 ELSE 0 END +";
    }
    if($standhome==0){
    		$req.="CASE WHEN m.result = 'D' AND m.team_2=c.id_team THEN 1 ELSE 0 END +";
    }
    $req.="0) as nul,";
    $req.="SUM(";
    if($standaway==0){
        	$req.="CASE WHEN m.result = '2' AND m.team_1=c.id_team THEN 1 ELSE 0 END +";
    }
    if($standhome==0){
    		$req.="CASE WHEN m.result = '1' AND m.team_2=c.id_team THEN 1 ELSE 0 END +";
    }
    $req.="0) as perdu 
    FROM team c 
    LEFT JOIN season_championship_team scc ON c.id_team=scc.id_team 
    LEFT JOIN matchday j ON (scc.id_season=j.id_season AND scc.id_championship=j.id_championship) 
    LEFT JOIN matchgame m ON m.id_matchday=j.id_matchday 
    WHERE scc.id_season=:id_season 
    AND scc.id_championship=:id_championship 
    AND (c.id_team=m.team_1 OR c.id_team=m.team_2) 
    AND m.result<>'' 
    GROUP BY c.id_team,c.name 
    ORDER BY points DESC, c.name ASC;";
    $data = $pdo->prepare($req,[
        'id_season' => $_SESSION['seasonId'],
        'id_championship' => $_SESSION['championshipId']
    ],true);
    
    $counter=0;
    $previousPoints=0;
    
    foreach ($data as $element)
    {
        echo "        <tr>\n";
        echo "          <td>";
        if($element->points!=$previousPoints){
            $counter++;
            echo $counter;
            $previousPoints=$element->points;
        }
        echo "</td>\n";
        echo "          <td>".$element->name."</td>\n";
        echo "          <td>".$element->points."</td>\n";
        echo "          <td>".$element->matchgame."</td>\n";
        echo "          <td>".$element->gagne."</td>\n";
        echo "          <td>".$element->nul."</td>\n";
        echo "          <td>".$element->perdu."</td>\n";
        echo "        </tr>\n";
    }
    $pdo->close();
}
echo "   </table>\n";
echo "</div>\n";
?>

