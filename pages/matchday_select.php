<?php
// Matchday select include file
require '../lang/fr.php';

$req = "SELECT DISTINCT id_matchday, number
FROM matchday
WHERE id_season=" . $_SESSION['seasonId']." 
AND id_championship=" . $_SESSION['championshipId'] . " ORDER BY number DESC;";
$response = $pdo->query($req);
$counter = $pdo->rowCount();

if($counter>0){
    // Select form
    $list = "<form action='index.php' method='POST'>\n";
    $list .= $form->labelBr($title_selectTheMatchday); 
    $list .= $form->selectSubmit("matchdaySelect", $response);
    $list .= "</form>\n";
    
    // Quicknav button
    $req = "SELECT DISTINCT j.id_matchday, j.number FROM matchday j
            LEFT JOIN matchgame m ON m.id_matchday=j.id_matchday
            WHERE m.result=''
            AND j.id_season=:id_season
            AND j.id_championship=:id_championship
            ORDER BY j.number;";
    $data = $pdo->prepare($req,[
        'id_season' => $_SESSION['seasonId'],
        'id_championship' => $_SESSION['championshipId']
    ]);
    $counter = $pdo->rowCount();
    if($counter>0){
        // $form->setValues($data);
        echo "<form action='index.php' method='POST'>\n";
        echo $form->label($title_quickNav);
        echo $form->inputHidden("matchdaySelect", $data->id_matchday . "," . $data->number);
        echo $form->submit("$icon_quicknav $title_MD".$data->number);
        echo "</form>\n";
    }
    
    echo $list;
    
} else echo "      <p>$title_noMatchday</p>\n";

?>