<?php 
/* Include to select a season or a championship */

// Select a season
if(empty($_SESSION['seasonId'])){
    $response = $db->query("SELECT * FROM season ORDER BY name;"); 
    echo "  <section>\n";
    echo "  	<form action='index.php' method='POST'>\n";             // Choisir
    echo "      <h1><big>&#127937;</big></h1><label>$title_selectTheSeason :</label>\n";                                    
    echo "  	<select name='seasonSelect' onchange='submit()'>\n";
    echo "  		<option value='0'>...</option>\n";

    while ($data = $response->fetch())
    {
        echo "  		<option value='".$data['id_season'].",".$data['name']."'>".$data['name']."</option>\n";
    }
    echo "	     </select>\n";
    echo "       <noscript><input type='submit'></noscript>\n";
    echo "	     </form>\n";
    
    $response = $db->query("SELECT * FROM season ORDER BY id_season DESC;"); 
    $data = $response->fetch();
    echo "  	<form action='index.php' method='POST'>";
    echo "<input type='hidden' name='seasonSelect' value='".$data['id_season'].",".$data['name']."'>";
    echo "<input type='submit' value='".$data['name']."'>";
    echo "</form>\n";
    
    echo "  </section>\n";
    $response->closeCursor();
}
// Select a championship
if($_SESSION['seasonId']>0){
    $response = $db->query("SELECT DISTINCT c.name FROM championship c 
    LEFT JOIN season_championship_team scc ON c.id_championship=scc.id_championship WHERE scc.id_season=".$_SESSION['seasonId']." ORDER BY c.name;"); 
    echo "  <section>\n";
    echo "  	<form action='index.php' method='POST'>\n";             // Choisir
    echo "    <h1><big>&#127942;</big></h1><label>$title_selectTheChampionship :</label>\n";                                    
    echo "  	<select name='championshipSelect' onchange='submit()'>\n";
    echo "  		<option value='0'>...</option>\n";

    while ($data = $response->fetch())
    {
        echo "  		<option value='".$data['id_championship'].",".$data['name']."'>".$data['name']."</option>\n";
    }
    echo "	 </select>\n";
    echo "      <noscript><input type='submit'></noscript>\n";
    echo "	 </form>\n";
    
    $response = $db->query("SELECT * FROM championship ORDER BY id_championship DESC;"); 
    $data = $response->fetch();
    echo "  	<form action='index.php' method='POST'>";
    echo "<input type='hidden' name='championshipSelect' value='".$data['id_championship'].",".$data['name']."'>";
    echo "<input type='submit' value='".$data['name']."'>";
    echo "</form>\n";
    
    echo "  </section>\n";
    $response->closeCursor();
}
?>
