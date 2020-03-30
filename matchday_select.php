<?php
// Matchday select include file
$response = $db->query("SELECT DISTINCT id_matchday, number FROM matchday 
        WHERE id_season=".$_SESSION['seasonId']." AND id_championship=".$_SESSION['championshipId']." ORDER BY number;");
                            
echo "  	<select name='matchdaySelect' onchange='submit()'>\n";
echo "  		<option value='0'>...</option>\n";

while ($data = $response->fetch(PDO::FETCH_OBJ))
{
    echo "  		<option value='".$data->id_matchday.",".$data->number."'>".$title_MD.$data->number."</option>\n";
}
echo "	 </select>\n";
$response->closeCursor();
?>
    
