<?php
// Matchday select include file
$response = $db->prepare("SELECT DISTINCT id_matchday, number 
FROM matchday 
WHERE id_season=:id_season 
AND id_championship=:id_championship ORDER BY number DESC;");
$response->execute([
    'id_season' => $_SESSION['seasonId'],
    'id_championship' => $_SESSION['championshipId']
]);

if($response->rowCount()>0){
    // Select form
    $list = "   <form action='index.php' method='POST'>\n";
    $list.= $form->labelBr($title_selectTheMatchday); 
    $list.= $form->selectSubmit("matchdaySelect", $response);
    
    // Quicknav button
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
        $form->setValues($data);
        echo "<form action='index.php' method='POST'>\n";
        echo $form->label($title_quickNav);
        echo $form->inputHidden("matchdaySelect", htmlentities($data->id_matchday).",".htmlentities($data->number));
        echo $form->submit("$icon_quicknav $title_MD".htmlentities($data->number));
        echo "</form>\n";
    }
    
    echo $list;
    
} else {
    echo "      <p>$title_noMatchday</p>\n";
}

?>
    
