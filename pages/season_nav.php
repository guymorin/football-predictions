<?php
// Season navigation include file
echo "  <nav>\n\t";

echo "<a href='/'>$title_homepage</a>";
echo "<a href='index.php?page=season&create=1'>$title_createASeason</a>\n";

$response = $db->query("SELECT * FROM season ORDER BY name;");
if($response->rowCount()>1){
    echo "<form action='index.php?page=season' method='POST'>\n";
    echo "      <input type='hidden' name='modify' value='1'>\n"; 
    echo "      <label>$title_modifyASeason :</label>\n";                                    
    echo "  	<select name='id_season' onchange='submit()'>\n";
    echo "  		<option value='0'>...</option>\n";
    
    
    while ($data = $response->fetch(PDO::FETCH_OBJ))
    {
        echo "  		<option value='".$data->id_season."'";
        if($data->id_season==$_SESSION['seasonId']) echo " disabled";
        echo ">".$data->name."</option>\n";
    }
    echo "	 </select>\n";
    echo "      <noscript><input type='submit'></noscript>\n";
    echo "	 </form>\n";
}
echo "      </nav>\n";
$response->closeCursor();

?>