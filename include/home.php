<?php 
/* Include to select a season or a championship */

// Select a season
if(empty($_SESSION['seasonId'])){
    $response = $db->query("SELECT * FROM season ORDER BY name;");
    echo "  <section>\n";
    echo "  	<form action='index.php' method='POST'>\n";
    echo "      <h1><big>$icon_season</big></h1><label>$title_selectTheSeason :</label><br />\n";                                    
    echo "  	<select name='seasonSelect' onchange='submit()'>\n";
    echo "  		<option value='0'>...</option>\n";

    while ($data = $response->fetch(PDO::FETCH_OBJ))
    {
        echo "  		<option value='".$data->id_season.",".$data->name."'>".$data->name."</option>\n";
    }
    echo "	     </select>\n";
    echo "       <br /><noscript><input type='submit' value='$title_select'></noscript>\n";
    echo "	     </form>\n";
    
    $response = $db->query("SELECT * FROM season ORDER BY id_season DESC;"); 
    $data = $response->fetch(PDO::FETCH_OBJ);
    echo "  	<form action='index.php' method='POST'>";
    echo "<input type='hidden' name='seasonSelect' value='".$data->id_season.",".$data->name."'>";
    echo "<input type='submit' value='".$data->name."'>";
    echo "</form>\n";
    
    echo "  </section>\n";
    $response->closeCursor();
}
// Select a championship
elseif($_SESSION['seasonId']>0){


// Display nav
    echo "<nav>\n";
    echo "  <a href='index.php?page=season&exit=1'>".$_SESSION['seasonName']." &#10060;</a>";
    echo "<a href='index.php?page=championship&create=1'>$title_createAChampionship</a>\n";
    echo "</nav>\n";
    echo "</nav>\n";
    
    echo "  <section>\n";
    echo "      <ul class='menu'>\n";

    // Display form
    $response = $db->query("SELECT DISTINCT c.id_championship, c.name
    FROM championship c
    ORDER BY c.name;");
    
    if($response->rowCount()>0){
         
        echo "  	<form action='index.php' method='POST'>\n";
        echo "      <h2>$icon_championship $title_championship</h2>\n";

        
        echo "        <label>$title_selectTheChampionship :</label><br />\n";                                    
        echo "  	  <select name='championshipSelect' onchange='submit()'>\n";
        echo "        		<option value='0'>...</option>\n";
        
        while ($data = $response->fetch(PDO::FETCH_OBJ))
        {
            echo "        		<option value='".$data->id_championship.",".$data->name."'>".$data->name."</option>\n";
        }
        echo "  	  </select>\n";
        echo "        <br /><noscript><input type='submit' value='$title_select'></noscript>\n";
        echo "  	 </form>\n";
        
        $response->execute([
            'id_season' => $_SESSION['seasonId']
        ]);
        
        $data = $response->fetch(PDO::FETCH_OBJ);
    
        echo "  	<form action='index.php' method='POST'>\n";
        echo "          <input type='hidden' name='championshipSelect' value='".$data->id_championship.",".$data->name."'>\n";
        echo "          <input type='submit' value='".$data->name."'>\n";
        echo "      </form>\n";
    }
    // No championship
    else {
        echo "      <h2>$title_noChampionship</h2>\n";
    }
    
    echo "      </ul>\n";
    echo "  </section>\n";

    $response->closeCursor();
}
?>
