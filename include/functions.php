<?php
function valOdds($val){
    require '../lang/fr.php';
    $odds = "<span>";
    switch($val){
        case($val<1.5):
            $odds .= $title_carefulToo;
            break;
        case($val<1.8):
            $odds .= $title_careful;
            break;
        case($val>3):
            $odds .= $title_speculativeToo;
            break;
        case($val>2.3):
            $odds .= $title_speculative;
            break;
    }
    $odds .= "</span>";
    return $odds;
}
function valRoi($val){
    require '../lang/fr.php';
    $roi="<span>";
    switch($val){
        case($val<0):
            $roi .= "$title_ROIisLosing";
            break;
        case($val==0):
            $roi .= "$title_ROIisNeutral";
            break;
        case($val>0&&$val<15):
            $roi .= "$title_ROIwins";
            break;
        case($val>=15):
            $roi .= "$title_ROIisExcellent";
            break;
    }
    $roi.="</span>";
    return $roi;
}
function valColor($val){
    switch($val){
        case $val>0:
            $color="green";
            break;
        case $val<0:
            $color="red";
            break;
        default:
            $color="black";
    }
    return $color;
}
function criterion($type,$data,$pdo){
    $v=0;
    switch($type){
        case "motivC1":
            if($data->motivation1!="") $v=$data->motivation1;
            else $v=1; // Avantage à domicile
            break;
        case "motivC2":
            if($data->motivation2!="") $v=$data->motivation2;
            break;
        case "serieC1":
            if($data->currentForm1!="") $v=$data->currentForm1;
            elseif(($_SESSION['matchdayNum']-1)>0){
                $num = ($_SESSION['matchdayNum']-1);
                $req="
                    SELECT m.team_1 as team FROM matchgame m
                    LEFT JOIN season_championship_team s ON s.id_team=m.team_1
                    LEFT JOIN matchday j ON j.id_matchday=m.id_matchday
                    WHERE j.number = :number
                    AND m.team_1 = :team_1
                    AND m.result = '1'
                    AND s.id_championship = :id_championship 
                    AND s.id_season = :id_season 
                    UNION
                    SELECT m.team_2 as team FROM matchgame m
                    LEFT JOIN season_championship_team s ON s.id_team=m.team_2
                    LEFT JOIN matchday j ON j.id_matchday=m.id_matchday
                    WHERE j.number = :number
                    AND m.team_2 = :team_1
                    AND m.result = '2'
                    AND s.id_championship = :id_championship
                    AND s.id_season = :id_season;";
                $r = $pdo->prepare($req,[
                    'number' => $num,
                    'team_1' => $data->eq1,
                    'id_championship' => $_SESSION['championshipId'],
                    'id_season' => $_SESSION['seasonId']
                ]);
                
                foreach($r as $v) $res[] = $v->team;
                
                if(in_array($data->eq1,$res)) $v=1;
            }
            break;
        case "serieC2":
            if($data->currentForm2!="") $v=$data->currentForm2;
            elseif(($_SESSION['matchdayNum']-1)>0){
                $num = ($_SESSION['matchdayNum']-1);
                $req="
                    SELECT m.team_1 as team FROM matchgame m
                    LEFT JOIN season_championship_team s ON s.id_team=m.team_1
                    LEFT JOIN matchday j ON j.id_matchday=m.id_matchday
                    WHERE j.number = :number 
                    AND m.team_1 = :team_2
                    AND m.result='1'
                    AND s.id_championship = :id_championship
                    AND s.id_season = :id_season 
                    UNION
                    SELECT m.team_2 as team FROM matchgame m
                    LEFT JOIN season_championship_team s ON s.id_team=m.team_2
                    LEFT JOIN matchday j ON j.id_matchday=m.id_matchday
                    WHERE j.number = :number 
                    AND m.team_2 = :team_2
                    AND m.result = '2'
                    AND s.id_championship = :id_championship
                    AND s.id_season = :id_season;";
                $r = $pdo->prepare($req,[
                    'number' => $num,
                    'team_2' => $data->eq2,
                    'id_championship' => $_SESSION['championshipId'],
                    'id_season' => $_SESSION['seasonId']
                ]);
                
                foreach($r as $v) $res[] = $v->team;
                
                if(in_array($data->eq2,$res)) $v=1;
            }
            break;
        case "v1":
            $req="SELECT marketValue FROM marketValue 
            WHERE id_team=:id_team  
            AND id_season=:id_season;";
            $r = $pdo->prepare($req,[
                'id_team' => $data->eq1,
                'id_season' => $_SESSION['seasonId']
            ]);
            $v = $r->marketValue;
            break;
        case "v2":
            $req="SELECT marketValue FROM marketValue 
            WHERE id_team=:id_team  
            AND id_season=:id_season;";
            $r = $pdo->prepare($req,[
                'id_team' => $data->eq2,
                'id_season' => $_SESSION['seasonId']
            ]);
            $v = $r->marketValue;
            break;
        case "predictionsHistoryHome":
            if(isset($data->Dom)) $v=$data->Dom;
            break;
        case "msNul":
            if(isset($data->Nul)) $v=$data->Nul;
            break;
        case "predictionsHistoryAway":
            if(isset($data->Ext)) $v=$data->Ext;
            break;
    }
    return $v;
}

function popup($texte,$lien){
    
    echo "  <div id='overlay'><div class='update'><a class='close' href='".$lien."'>&times;</a><p>".$texte."</p><p><a href='".$lien."'>Ok</a></p></div></div>\n";
    
}
function changeMD($pdo,$page){
    require '../lang/fr.php';

    // Arrows to change matchday
    echo "<div id='changeMD'>\n";
    $req = "SELECT id_matchday, number FROM matchday
        WHERE number >= :match1
        AND number <> :match2
        AND number <= :match3
        AND id_season = :id_season
        AND id_championship = :id_championship
        ORDER BY number;";
    $data = $pdo->prepare($req,[
        'match1' => ($_SESSION['matchdayNum']-1),
        'match2' => $_SESSION['matchdayNum'],
        'match3' => ($_SESSION['matchdayNum']+1),
        'id_season' => $_SESSION['seasonId'],
        'id_championship' => $_SESSION['championshipId']
    ]);
    $counter = $pdo->rowCount();

    $button1 = $button2 = "";
    foreach ($data as $d)
    {
        switch($counter){
            case 1:
            case 2:
                // Previous button
                if($d->number == $_SESSION['matchdayNum']-1){
                    $button1 = "  <input type='submit' value='&larr;'>\n";
                    $button1 .= "<input type='hidden' name='matchdaySelect' ";
                    $button1 .= "value='".$d->id_matchday.",".$d->number."'>\n";
                }
                // Next button
                if($d->number==$_SESSION['matchdayNum']+1){
                    $button2 = "  <input type='submit' value='&rarr;'>\n";
                    $button2 .= "<input type='hidden' name='matchdaySelect' ";
                    $button2 .= "value='".$d->id_matchday.",".$d->number."'>\n";
                }
                break;
        }
        $counter--;
    }
    echo "<form id='leftArrow' action='index.php?page=$page' method='POST'>\n";
    echo $button1;
    echo "</form>\n";
    echo "<form id='rightArrow' action='index.php?page=$page' method='POST'>\n";
    echo $button2;
    echo "</form>\n";
    echo "</div>\n";
    
}
if (!function_exists('array_key_first')){
    function array_key_first(array $arr){
        foreach($arr as $key => $unused){
            return $key;
        }
        return NULL;
    }
}
if (! function_exists("array_key_last")){
    function array_key_last($array){
        if (!is_array($array) || empty($array)){
            return NULL;
        }
       
        return array_keys($array)[count($array)-1];
    }
}
?>
 
