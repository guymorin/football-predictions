<?php
// Championship navigation include file
echo "<nav>\n";

if(isset($_SESSION['championshipId'])){
    echo "  	<a href='/'>$title_homepage</a>";
    echo "<a href='index.php?page=dashboard'>$title_dashboard</a>";
    echo "<a href='index.php?page=championship'>$title_standing</a>";
} else echo "      <a href='index.php?page=season&exit=1'>".$_SESSION['seasonName']." &#10060;</a>";

echo "<a href='index.php?page=championship&create=1'>$title_createAChampionship</a>\n";

$response = $db->query("SELECT DISTINCT c.id_championship, c.name 
FROM championship c 
ORDER BY c.name;");
if($response->rowCount()>1){
    echo "  	<form action='index.php?page=championship' method='POST'>\n";
    echo "         <input type='hidden' name='modify' value='1'>\n";
    echo "         <label>$title_modifyAChampionship :</label>\n";
    echo "  	   <select name='id_championship' onchange='submit()'>\n";
    echo "  	       <option value='0'>...</option>\n";
    
    while ($data = $response->fetch(PDO::FETCH_OBJ))
    {
        echo "      	   <option value='".$data->id_championship."'";
        if($data->id_championship==$_SESSION['championshipId']) echo " disabled";
        echo ">".$data->name."</option>\n";
    }
    echo "	       </select>\n";
    echo "         <noscript><input type='submit'></noscript>\n";
    echo "	    </form>\n";
}

echo "</nav>\n";
$response->closeCursor();

?>
