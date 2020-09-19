<?php
use FootballPredictions\Language;

function result($type,$pdo,$d='',$team1Weather=0,$team2Weather=0){
    $r='';
    switch($type){
        case "selectCriterion":
            $req="SELECT DISTINCT m.id_matchgame,
            cr.motivation1,cr.motivation2,
            cr.currentForm1,cr.currentForm2,
            cr.physicalForm1,cr.physicalForm2,
            cr.weather1,cr.weather2,
            cr.bestPlayers1,cr.bestPlayers2,
            cr.marketValue1,cr.marketValue2,
            cr.home_away1,cr.home_away2,
            c1.name as name1,c2.name as name2,c1.id_team as eq1,c2.id_team as eq2,
            c1.weather_code,
            m.result, m.date FROM matchgame m
            LEFT JOIN team c1 ON m.team_1=c1.id_team
            LEFT JOIN team c2 ON m.team_2=c2.id_team
            LEFT JOIN criterion cr ON cr.id_matchgame=m.id_matchgame
            LEFT JOIN matchday md ON md.id_matchday=m.id_matchday
            WHERE md.id_matchday=:id_matchday
            AND md.id_season=:id_season
            AND md.id_championship = :id_championship
            ORDER BY m.date;";
            $r = $pdo->prepare($req,[
                'id_season' => $_SESSION['seasonId'],
                'id_championship' => $_SESSION['championshipId'],
                'id_matchday' => $_SESSION['matchdayId']
            ],true);
            break;
        case "bestHome":
            $req="
            SELECT c.id_team, c.name, COUNT(m.id_matchgame) as matchs,
            SUM(
                CASE WHEN m.result = '1' AND m.team_1=c.id_team THEN 3 ELSE 0 END +
                CASE WHEN m.result = 'D' AND m.team_1=c.id_team THEN 1 ELSE 0 END
            ) as points
            FROM team c
            LEFT JOIN season_championship_team scc ON c.id_team=scc.id_team
            LEFT JOIN matchday j ON (scc.id_season=j.id_season AND scc.id_championship=j.id_championship)
            LEFT JOIN matchgame m ON m.id_matchday=j.id_matchday
            WHERE scc.id_season=:id_season
            AND scc.id_championship=:id_championship
            AND (c.id_team=m.team_1 OR c.id_team=m.team_2)
            AND m.result<>''
            GROUP BY c.id_team,c.name
            ORDER BY points DESC
            LIMIT 0,5";
            $r = $pdo->prepare($req,[
                'id_season' => $_SESSION['seasonId'],
                'id_championship' => $_SESSION['championshipId']
            ],true);
            break;
        case "worstHome":
            $req="
            SELECT c.id_team, c.name, COUNT(m.id_matchgame) as matchs,
            SUM(
                CASE WHEN m.result = '1' AND m.team_1=c.id_team THEN 3 ELSE 0 END +
                CASE WHEN m.result = 'D' AND m.team_1=c.id_team THEN 1 ELSE 0 END
            ) as points
            FROM team c
            LEFT JOIN season_championship_team scc ON c.id_team=scc.id_team
            LEFT JOIN matchday j ON (scc.id_season=j.id_season AND scc.id_championship=j.id_championship)
            LEFT JOIN matchgame m ON m.id_matchday=j.id_matchday
            WHERE scc.id_season=:id_season
            AND scc.id_championship=:id_championship
            AND (c.id_team=m.team_1 OR c.id_team=m.team_2)
            AND m.result<>''
            GROUP BY c.id_team,c.name
            ORDER BY points ASC
            LIMIT 0,5";
            $r = $pdo->prepare($req,[
                'id_season' => $_SESSION['seasonId'],
                'id_championship' => $_SESSION['championshipId']
            ],true);
            break;
        case "bestAway":
            $req="
            SELECT c.id_team, c.name, COUNT(m.id_matchgame) as matchs,
            SUM(
                CASE WHEN m.result = '1' AND m.team_2=c.id_team THEN 3 ELSE 0 END +
                CASE WHEN m.result = 'D' AND m.team_2=c.id_team THEN 1 ELSE 0 END
            ) as points
            FROM team c
            LEFT JOIN season_championship_team scc ON c.id_team=scc.id_team
            LEFT JOIN matchday j ON (scc.id_season=j.id_season AND scc.id_championship=j.id_championship)
            LEFT JOIN matchgame m ON m.id_matchday=j.id_matchday
            WHERE scc.id_season=:id_season
            AND scc.id_championship=:id_championship
            AND (c.id_team=m.team_1 OR c.id_team=m.team_2)
            AND m.result<>''
            GROUP BY c.id_team,c.name
            ORDER BY points ASC
            LIMIT 0,5";
            $r = $pdo->prepare($req,[
                'id_season' => $_SESSION['seasonId'],
                'id_championship' => $_SESSION['championshipId']
            ],true);
            break;
        case "worstAway":
            $req="
            SELECT c.id_team, c.name, COUNT(m.id_matchgame) as matchs,
            SUM(
                CASE WHEN m.result = '1' AND m.team_2=c.id_team THEN 3 ELSE 0 END +
                CASE WHEN m.result = 'D' AND m.team_2=c.id_team THEN 1 ELSE 0 END
            ) as points
            FROM team c
            LEFT JOIN season_championship_team scc ON c.id_team=scc.id_team
            LEFT JOIN matchday j ON (scc.id_season=j.id_season AND scc.id_championship=j.id_championship)
            LEFT JOIN matchgame m ON m.id_matchday=j.id_matchday
            WHERE scc.id_season=:id_season
            AND scc.id_championship=:id_championship
            AND (c.id_team=m.team_1 OR c.id_team=m.team_2)
            AND m.result<>''
            GROUP BY c.id_team,c.name
            ORDER BY points DESC
            LIMIT 0,5";
            $r = $pdo->prepare($req,[
                'id_season' => $_SESSION['seasonId'],
                'id_championship' => $_SESSION['championshipId']
            ],true);
            break;
        case "history":
            $req="SELECT SUM(CASE WHEN m.result = '1' THEN 1 ELSE 0 END) AS Home,
            SUM(CASE WHEN m.result = 'D' THEN 1 ELSE 0 END) AS Draw,
            SUM(CASE WHEN m.result = '2' THEN 1 ELSE 0 END) AS Away
            FROM matchgame m
            LEFT JOIN criterion cr ON cr.id_matchgame=m.id_matchgame
            WHERE cr.motivation1 = :motivation1
            AND cr.motivation2 = :motivation2
            AND cr.currentForm1 = :currentForm1
            AND cr.currentForm2 = :currentForm2
            AND cr.physicalForm1 = :physicalForm1
            AND cr.physicalForm2 = :physicalForm2
            AND cr.weather1 = :weather1
            AND cr.weather2 = :weather2
            AND cr.bestPlayers1 = :bestPlayers1
            AND cr.bestPlayers2 = :bestPlayers2
            AND cr.marketValue1 = :marketValue1
            AND cr.marketValue2 = :marketValue2
            AND cr.home_away1 = :home_away1
            AND cr.home_away2 = :home_away2
            AND m.date < :mdate;";
            $r = $pdo->prepare($req,[
                'motivation1' => $d->motivation1,
                'motivation2' => $d->motivation2,
                'currentForm1' => $d->currentForm1,
                'currentForm2' => $d->currentForm2,
                'physicalForm1' => $d->physicalForm1,
                'physicalForm2' => $d->physicalForm2,
                'weather1' => $team1Weather,
                'weather2' => $team2Weather,
                'bestPlayers1' => $d->bestPlayers1,
                'bestPlayers2' => $d->bestPlayers2,
                'marketValue1' => $d->marketValue1,
                'marketValue2' => $d->marketValue2,
                'home_away1' => $d->home_away1,
                'home_away2' => $d->home_away2,
                'mdate' => $d->date
            ]);
            break;
    }
    return $r;
}

function valOdds($val){
    
    $odds = "<span>";
    switch($val){
        case($val<1.5):
            $odds .= Language::title('carefulToo');
            break;
        case($val<1.8):
            $odds .= Language::title('careful');
            break;
        case($val>3):
            $odds .= Language::title('speculativeToo');
            break;
        case($val>2.3):
            $odds .= Language::title('speculative');
            break;
    }
    $odds .= "</span>";
    return $odds;
}
function valRoi($val){
    
    $roi="<span>";
    switch($val){
        case($val<0):
            $roi .= Language::title('ROIisLosing');
            break;
        case($val==0):
            $roi .= Language::title('ROIisNeutral');
            break;
        case($val>0&&$val<15):
            $roi .= Language::title('ROIwins');
            break;
        case($val>=15):
            $roi .= Language::title('ROIisExcellent');
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
                
                if($r == null) $v=0;
                else {
                    foreach($r as $valTeam) $res[] = $valTeam->team;
                    if(in_array($data->eq1,$res)) $v=1;
                }
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
                
                if($r == null) $v=0;
                else {
                    foreach($r as $valTeam) $res[] = $valTeam->team;
                    if(in_array($data->eq2,$res)) $v=1;
                }                
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
            if(isset($data->Home)) $v=$data->Home;
            break;
        case "predictionsHistoryDraw":
            if(isset($data->Draw)) $v=$data->Draw;
            break;
        case "predictionsHistoryAway":
        if(isset($data->Away)) $v=$data->Away;
            break;
    }
    return $v;
}

function popup($text,$link){
    echo "<div id='overlay'><div class='update'><p class='close'><a href='".$link."'>&times;</a></p><p>".$text."</p><p><a href='".$link."' id='ok'>Ok</a></p></div></div>\n";    
    echo "<script>document.getElementById('ok').focus();</script>";
}

function changeMD($pdo,$page){
    

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
    ],true);
    $counter = $pdo->rowCount();

    $button1 = $button2 = "";
    foreach ($data as $d)
    {
        switch($counter){
            case 1:
            case 2:
                // Previous button
                if($d->number == $_SESSION['matchdayNum']-1){
                    $button1 = "<input type='submit' value='&larr;'>\n";
                    $button1 .= "<input type='hidden' name='matchdaySelect' ";
                    $button1 .= "value='".$d->id_matchday.",".$d->number."'>\n";
                }
                // Next button
                if($d->number==$_SESSION['matchdayNum']+1){
                    $button2 = "<input type='submit' value='&rarr;'>\n";
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
