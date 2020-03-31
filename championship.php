<?php
/* This is the Football Predictions championship section page */
/* Author : Guy Morin */

// Files to include
require("championship_nav.php");

echo "<section>\n";

// Values
$error = new Errors();
$championshipId=$create=$modify=$delete=$standaway=$standhome=0;
$championshipName="";
if(isset($_POST['id_championship'])) $championshipId=$error->check("Digit",$_POST['id_championship']);
if(isset($_POST['name'])) $championshipName=$error->check("Alnum",$_POST['name'],$error);

if(isset($_GET['create'])) $create=$error->check("Action",$_GET['create']);
elseif(isset($_POST['create'])) $create=$error->check("Action",$_POST['create']);
if(isset($_POST['modify'])) $modify=$error->check("Action",$_POST['modify']);
if(isset($_POST['delete'])) $delete=$error->check("Action",$_POST['delete']);
if(isset($_GET['standhome'])) $standhome=$error->check("Action",$_GET['standhome']);
if(isset($_GET['standaway'])) $standaway=$error->check("Action",$_GET['standaway']);

// First, select a championship
if(
    (empty($_SESSION['championshipId']))
    &&($create==0)
    &&($modify==0)
    &&($delete==0)
    ){
    echo "      <ul class='menu'>\n";
    $response = $db->query("SELECT DISTINCT c.id_championship, c.name
    FROM championship c
    ORDER BY c.name;");
    $list="";
    if($response->rowCount()>0){
        // Select form
        $list.="  	<form action='index.php' method='POST'>\n";
        $list.="      <h2>$icon_championship $title_championship</h2>\n";
        $list.="        <label>$title_selectTheChampionship :</label><br />\n";
        $list.="  	  <select name='championshipSelect' onchange='submit()'>\n";
        $list.="        		<option value='0'>...</option>\n";
        while ($data = $response->fetch(PDO::FETCH_OBJ))
        {
            $list.="        		<option value='".$data->id_championship.",".$data->name."'>".$data->name."</option>\n";
        }
        $list.="  	  </select>\n";
        $list.="        <br /><noscript><input type='submit' value='$title_select'></noscript>\n";
        $list.="  	</form>\n";
        
        // Quick nav button
        $response->execute([
            'id_season' => $_SESSION['seasonId']
        ]);
        $data = $response->fetch(PDO::FETCH_OBJ);
        echo "  	<form action='index.php' method='POST'>\n";
        echo "      <label>$title_quickNav :</label><br />\n";
        echo "          <input type='hidden' name='championshipSelect' value='".$data->id_championship.",".$data->name."'>\n";
        echo "          <input type='submit' value='$icon_quicknav ".$data->name."'>\n";
        echo "      </form>\n";
        
        echo $list;
    }
    // No championship
    else {
        echo "      <h2>$title_noChampionship</h2>\n";
    }
    echo "      </ul>\n";
    
    $response->closeCursor();
}
/* Popups or page */
// Deleted popup
elseif($delete==1){
    if($championshipId==0){
        popup($title_error,"index.php?page=championship");
    } else {
        $req="DELETE FROM championship WHERE id_championship='".$championshipId."';";
        $db->exec($req);
        $db->exec("ALTER TABLE championship AUTO_INCREMENT=0;");
        popup($title_deleted,"index.php?page=championship");
    }
}
// Created popup or create form
elseif($create==1){
    echo "<h3>$title_createAChampionship</h3>\n";
    // Create form
    if($championshipName=="") {
        echo "<div class='error'>".$error->getError()."</div>\n";
        echo "	 <form action='index.php?page=championship' method='POST'>\n";
        echo "     <input type='hidden' name='create' value='1'>\n";
        echo "	   <label>$title_name</label>\n";
        echo "     <input type='text' name='name' value='";
        if($_POST['name']) echo $_POST['name'];
        else echo $championshipName;
        echo "'>\n";
        echo "     <input type='submit' value='$title_create'>\n";
        echo "	 </form>\n";   
    }
    // Created popup
    else {
        $db->exec("ALTER TABLE championship AUTO_INCREMENT=0;");
        $req="INSERT INTO championship VALUES(NULL,'".$championshipName."');";
        $db->exec($req);
        popup($title_created,"index.php?page=championship");
    }
}
// Modified popup or modify form
elseif($modify==1){
    echo "<h3>$title_modifyAChampionship</h3>\n";
    if($championshipId==0) {
        popup($title_error,"index.php?page=championship");
    }
    // Modify form
    elseif($championshipName=="") {
        $response = $db->query("SELECT * FROM championship WHERE id_championship='$championshipId';");
        echo "<div class='error'>".$error->getError()."</div>\n";
        echo "	 <form action='index.php?page=championship' method='POST'>\n";
        $data = $response->fetch(PDO::FETCH_OBJ);
        echo "      <input type='hidden' name='modify' value='1'>\n";
        echo "      <input type='hidden' name='id_championship' readonly='readonly' value='".$data->id_championship."'>\n";
        echo "	    <label>$title_name</label>\n";
        echo "     <input type='text' name='name' value='";
        if($_POST['name']) echo $_POST['name'];
        else echo $data->name;
        echo "'>\n";echo "      <input type='submit' value='$title_modify'>\n";
        echo "	 </form>\n";
        
        echo "	 <form action='index.php?page=championship' method='POST' onsubmit='return confirm()'>\n";
        echo "      <input type='hidden' name='delete' value=1>\n";
        echo "      <input type='hidden' name='id_championship' value=$championshipId>\n";
        echo "      <input type='hidden' name='name' value='".$data->name."'>\n";
        echo "      <input type='submit' value='&#9888 $title_delete ".$data->name." &#9888'>\n"; // Bouton Supprimer
        echo "	 </form>\n";
        $response->closeCursor();
    }
    // Modify popup
    else {
        $req="UPDATE championship SET name='$championshipName' WHERE id_championship='$championshipId';";
        $db->exec($req);
        popup($title_modified,"index.php?page=championship");
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
    WHERE scc.id_season='".$_SESSION['seasonId']."' 
    AND scc.id_championship='".$_SESSION['championshipId']."' 
    AND (c.id_team=m.team_1 OR c.id_team=m.team_2) 
    AND m.result<>'' 
    GROUP BY c.id_team,c.name 
    ORDER BY points DESC, c.name ASC;";
    $response = $db->query($req);
    $counter=0;
    $previousPoints=0;
    
    while ($data = $response->fetch(PDO::FETCH_OBJ))
    {
        echo "        <tr>\n";
        echo "          <td>";
        if($data->points!=$previousPoints){
            $counter++;
            echo $counter;
            $previousPoints=$data->points;
        }
        echo "</td>\n";
        echo "          <td>".$data->name."</td>\n";
        echo "          <td>".$data->points."</td>\n";
        echo "          <td>".$data->matchgame."</td>\n";
        echo "          <td>".$data->gagne."</td>\n";
        echo "          <td>".$data->nul."</td>\n";
        echo "          <td>".$data->perdu."</td>\n";
        echo "        </tr>\n";
    }
    $response->closeCursor();
}
echo "   </table>\n";
echo "</div>\n";
echo "</section>\n";
?>
     
