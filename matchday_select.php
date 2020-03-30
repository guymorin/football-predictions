<?php
// Matchday select include file

$response = $db->prepare("SELECT DISTINCT id_matchday, number 
FROM matchday 
WHERE id_season=:id_season 
AND id_championship=:id_championship ORDER BY number;");
$response->execute([
    'id_season' => $_SESSION['seasonId'],
    'id_championship' => $_SESSION['championshipId']
]);
if($response->rowCount()>0){
    echo "   <label>$title_selectTheMatchday :</label><br />\n"; 
    echo "   <form action='index.php' method='POST'>\n";
    echo "    <select name='matchdaySelect' onchange='submit()'>\n";
    echo "  		<option value='0'>...</option>\n";
    while ($data = $response->fetch(PDO::FETCH_OBJ))
    {
        echo "  		<option value='".$data->id_matchday.",".$data->number."'>".$title_MD.$data->number."</option>\n";
    }
    echo "	 </select>\n";
    $response->closeCursor();
    echo "      <br /><noscript><input type='submit' value='$title_select'></noscript>\n";
    echo "	 </form>\n";
    
    $response = $db->prepare("SELECT DISTINCT j.id_matchday, j.number FROM matchday j
            LEFT JOIN matchgame m ON m.id_matchday=j.id_matchday
            WHERE m.result=''
            AND j.id_season=:id_season
            AND j.id_championship=:id_championship
            ORDER BY j.number;");
    $response->execute([
        'id_season' => $_SESSION['seasonId'],
        'id_championship' => $_SESSION['championshipId']
    ]);
    if($response->rowCount()>0){
        $data = $response->fetch(PDO::FETCH_OBJ);
        echo "  <form action='index.php' method='POST'>\n";
        echo "     <input type='hidden' name='matchdaySelect' value='".htmlentities($data->id_matchday).",".htmlentities($data->number)."'>\n";
        echo "     <input type='submit' value='$title_MD".htmlentities($data->number)."'>\n";
        echo "  </form>\n";
    }
    
} else {
    echo "      <p>$title_noMatchday</p>\n";
}

?>
    
