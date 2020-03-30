<?php
function valOdds($val){
    include("lang/fr.php");
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
    include("lang/fr.php");
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
function criterion($type,$data,$db){
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
            elseif(($_SESSION['matchdayNum']-1)>0) {
                $num = ($_SESSION['matchdayNum']-1);
                $req="
                    SELECT m.team_1 as team FROM matchgame m
                    LEFT JOIN season_championship_team s ON s.id_team=m.team_1
                    LEFT JOIN matchday j ON j.id_matchday=m.id_matchday
                    WHERE j.number='".$num."'
                    AND m.team_1='".$data->eq1."'
                    AND m.result='1'
                    AND s.id_championship='".$_SESSION['championshipId']."'
                    AND s.id_season='".$_SESSION['seasonId']."'
                    UNION
                    SELECT m.team_2 as team FROM matchgame m
                    LEFT JOIN season_championship_team s ON s.id_team=m.team_2
                    LEFT JOIN matchday j ON j.id_matchday=m.id_matchday
                    WHERE j.number='".$num."'
                    AND m.team_2='".$data->eq1."'
                    AND m.result='2'
                    AND s.id_championship='".$_SESSION['championshipId']."'
                    AND s.id_season='".$_SESSION['seasonId']."'";
                $r = $db->query($req);
                while($data=$r->fetchColumn(0))   $res[] = $data;
                if(in_array($data->eq1,$res)) $v=1;
            }
            break;
        case "serieC2":
            if($data->currentForm2!="") $v=$data->currentForm2;
            elseif(($_SESSION['matchdayNum']-1)>0) {
                $num = ($_SESSION['matchdayNum']-1);
                $req="
                    SELECT m.team_1 as team FROM matchgame m
                    LEFT JOIN season_championship_team s ON s.id_team=m.team_1
                    LEFT JOIN matchday j ON j.id_matchday=m.id_matchday
                    WHERE j.number='".$num."'
                    AND m.team_1='".$data->eq2."'
                    AND m.result='1'
                    AND s.id_championship='".$_SESSION['championshipId']."'
                    AND s.id_season='".$_SESSION['seasonId']."'
                    UNION
                    SELECT m.team_2 as team FROM matchgame m
                    LEFT JOIN season_championship_team s ON s.id_team=m.team_2
                    LEFT JOIN matchday j ON j.id_matchday=m.id_matchday
                    WHERE j.number='".$num."'
                    AND m.team_2='".$data->eq2."'
                    AND m.result='2'
                    AND s.id_championship='".$_SESSION['championshipId']."'
                    AND s.id_season='".$_SESSION['seasonId']."'";
                $r = $db->query($req);
                while($data=$r->fetchColumn(0))   $res[] = $data;
                if(in_array($data->eq2,$res)) $v=1;
            }
            break;
        case "v1":
            $req="SELECT marketValue FROM marketValue WHERE id_team='".$data->eq1."' AND id_season='".$_SESSION['seasonId']."';";
            $r = $db->query($req)->fetch(PDO::FETCH_OBJ);
            $v = $r[0];
            break;
        case "v2":
            $req="SELECT marketValue FROM marketValue WHERE id_team='".$data->eq2."' AND id_season='".$_SESSION['seasonId']."';";
            $r = $db->query($req)->fetch(PDO::FETCH_OBJ);
            $v = $r[0];
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
function changeMD($db,$page){
    include("lang/fr.php");
    // Arrows to change matchday
    echo "<div id='changeMD'>\n";
    $req="SELECT id_matchday, number FROM matchday
        WHERE number>=".($_SESSION['matchdayNum']-1)."
        AND number<>".$_SESSION['matchdayNum']."
        AND number<=".($_SESSION['matchdayNum']+1)."
        AND id_season='".$_SESSION['seasonId']."'
        AND id_championship='".$_SESSION['championshipId']."'
        ORDER BY number;";
    $response = $db->query($req);
    $nb=sizeof($response->fetchAll());
    $button1=$button2="";
    $response = $db->query($req);
    while ($data = $response->fetch(PDO::FETCH_OBJ))
    {
        switch($nb){
            case 1:
            case 2:
                // Previous button
                if($data->number==$_SESSION['matchdayNum']-1){
                    $button1="  <input type='submit' value='<'>\n";
                    $button1.="<input type='hidden' name='matchdaySelect' ";
                    $button1.="value='".$data->id_matchday.",".$data->number."'>\n";
                }
                // Next button
                if($data->number==$_SESSION['matchdayNum']+1){
                    $button2="  <input type='submit' value='>'>\n";
                    $button2.="<input type='hidden' name='matchdaySelect' ";
                    $button2.="value='".$data->id_matchday.",".$data->number."'>\n";
                }
                break;
        }
        $nb--;
    }
    echo "<form action='index.php?page=$page' method='POST'>\n";
    echo $button1;
    echo "</form>\n";
    echo "<form action='index.php?page=$page' method='POST'>\n";
    echo $button2;
    echo "</form>\n";
    echo "</div>\n";
    $response->closeCursor();
}
if (!function_exists('array_key_first')) {
    function array_key_first(array $arr) {
        foreach($arr as $key => $unused) {
            return $key;
        }
        return NULL;
    }
}
if (! function_exists("array_key_last")) {
    function array_key_last($array) {
        if (!is_array($array) || empty($array)) {
            return NULL;
        }
       
        return array_keys($array)[count($array)-1];
    }
}
?>
 
