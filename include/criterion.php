<?php 


function criterion($type,$data,$pdo){
    $v=0;
    switch($type){
        case "motivC1":
            if($data->motivation1!="") $v=$data->motivation1;
            else $v=1; // default is for home team
            break;
        case "motivC2":
            if($data->motivation2!="") $v=$data->motivation2;
            break;
        case "physicalC1":
            if($data->physicalForm1!="") $v=$data->physicalForm1;
            elseif(($_SESSION['matchdayNum']-1)>0){
                $num = ($_SESSION['matchdayNum']-1);
                $req="
                    SELECT m.team_1 as team FROM matchgame m
                    LEFT JOIN season_championship_team s ON s.id_team=m.team_1
                    LEFT JOIN matchday j ON j.id_matchday=m.id_matchday
                    WHERE j.number = :number
                    AND m.team_1 = :team_1
                    AND m.red1 > '0'
                    AND s.id_championship = :id_championship
                    AND s.id_season = :id_season
                    UNION
                    SELECT m.team_2 as team FROM matchgame m
                    LEFT JOIN season_championship_team s ON s.id_team=m.team_2
                    LEFT JOIN matchday j ON j.id_matchday=m.id_matchday
                    WHERE j.number = :number
                    AND m.team_2 = :team_1
                    AND m.red2 > '0'
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
                    $res = array();
                    foreach($r as $valTeam){
                        $res[] = $valTeam;
                    }
                    if(in_array($data->eq1,$res)) $v='-1';
                }
            }
            break;
        case "physicalC2":
            if($data->physicalForm2!="") $v=$data->physicalForm2;
            elseif(($_SESSION['matchdayNum']-1)>0){
                $num = ($_SESSION['matchdayNum']-1);
                // Did the team have a red card in the last matchday ?
                $req="
                    SELECT m.team_1 as team FROM matchgame m
                    LEFT JOIN season_championship_team s ON s.id_team=m.team_1
                    LEFT JOIN matchday j ON j.id_matchday=m.id_matchday
                    WHERE j.number = :number
                    AND m.team_1 = :team_2
                    AND m.red1>'0'
                    AND s.id_championship = :id_championship
                    AND s.id_season = :id_season
                    UNION
                    SELECT m.team_2 as team FROM matchgame m
                    LEFT JOIN season_championship_team s ON s.id_team=m.team_2
                    LEFT JOIN matchday j ON j.id_matchday=m.id_matchday
                    WHERE j.number = :number
                    AND m.team_2 = :team_2
                    AND m.red2 > '0'
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
                    $res = array();
                    foreach($r as $valTeam){
                        $res[] = $valTeam;
                    }
                    if(in_array($data->eq2,$res)) $v='-1';
                }
            }
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
                    AND j.id_championship = :id_championship
                    AND j.id_season = :id_season
                    UNION
                    SELECT m.team_2 as team FROM matchgame m
                    LEFT JOIN season_championship_team s ON s.id_team=m.team_2
                    LEFT JOIN matchday j ON j.id_matchday=m.id_matchday
                    WHERE j.number = :number
                    AND m.team_2 = :team_1
                    AND m.result = '2'
                    AND j.id_championship = :id_championship
                    AND j.id_season = :id_season;";
                $r = $pdo->prepare($req,[
                    'number' => $num,
                    'team_1' => $data->eq1,
                    'id_championship' => $_SESSION['championshipId'],
                    'id_season' => $_SESSION['seasonId']
                ]);
                
                if($r == null) $v=0;
                else {
                    $res = array();
                    foreach($r as $valTeam){
                        $res[] = $valTeam;
                    }
                    if(in_array($data->eq1,$res)) $v=1;
                }
            }
            break;
        case "serieC2":
            if($data->currentForm2!="") $v=$data->currentForm2;
            elseif(($_SESSION['matchdayNum']-1)>0){
                $num = ($_SESSION['matchdayNum']-1);
                // Did the team win in the last matchday ?
                $req="
                    SELECT m.team_1 as team FROM matchgame m
                    LEFT JOIN season_championship_team s ON s.id_team=m.team_1
                    LEFT JOIN matchday j ON j.id_matchday=m.id_matchday
                    WHERE j.number = :number
                    AND m.team_1 = :team_2
                    AND m.result='1'
                    AND j.id_championship = :id_championship
                    AND j.id_season = :id_season
                    UNION
                    SELECT m.team_2 as team FROM matchgame m
                    LEFT JOIN season_championship_team s ON s.id_team=m.team_2
                    LEFT JOIN matchday j ON j.id_matchday=m.id_matchday
                    WHERE j.number = :number
                    AND m.team_2 = :team_2
                    AND m.result = '2'
                    AND j.id_championship = :id_championship
                    AND j.id_season = :id_season;";
                $r = $pdo->prepare($req,[
                    'number' => $num,
                    'team_2' => $data->eq2,
                    'id_championship' => $_SESSION['championshipId'],
                    'id_season' => $_SESSION['seasonId']
                ]);
                
                if($r == null) $v=0;
                else {
                    $res = array();
                    foreach($r as $valTeam){
                        $res[] = $valTeam;
                    }
                    if(in_array($data->eq2,$res)) $v=1;
                }
            }
            break;
        case "trendTeam1":
            $v=trend($data->eq1,$pdo);
            break;
        case "trendTeam2":
            $v=trend($data->eq2,$pdo);
            break;
        case "v1":
            $v=value($data->eq1,$pdo);
            break;
        case "v2":
            $v=value($data->eq2,$pdo);
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

function trend($team,$pdo){
    // Return the team championship results on the last three matches
    $val=0;
    $req="SELECT SUM(t.cnt) as trend FROM (
                    (SELECT
                        CASE
                            WHEN mg.result = '1' THEN '3'
                            WHEN mg.result = 'D' THEN '1'
                            ELSE '0'
                        END as cnt
                        FROM matchgame mg
                        LEFT JOIN matchday md ON md.id_matchday=mg.id_matchday
                        WHERE mg.team_1=:id_team
                        AND md.number IN (:num1,:num2,:num3)
                        AND md.id_season=:id_season
                        AND md.id_championship=:id_championship)
                    UNION ALL
                    (SELECT
                        CASE
                            WHEN mg.result='2' THEN '3'
                            WHEN mg.result='D' THEN '1'
                            ELSE '0'
                        END as cnt
                        FROM matchgame mg
                        LEFT JOIN matchday md ON md.id_matchday=mg.id_matchday
                        WHERE mg.team_2=:id_team
                        AND md.number IN (:num1,:num2,:num3)
                        AND md.id_season=:id_season
                        AND md.id_championship=:id_championship)
                    ) t;";
    $r = $pdo->prepare($req,[
        'id_team' => $team,
        'id_season' => $_SESSION['seasonId'],
        'id_championship' => $_SESSION['championshipId'],
        'num1' => ($_SESSION['matchdayNum'] - 3),
        'num2' => ($_SESSION['matchdayNum'] - 2),
        'num3' => ($_SESSION['matchdayNum'] - 1)
    ]);
    $val = $r->trend;
    return $val;
}
function value($team,$pdo){
    // Select the market value of the team 1
    $val = 0;
    $req="SELECT marketValue FROM marketValue
            WHERE id_team=:id_team
            AND id_season=:id_season;";
    $r = $pdo->prepare($req,[
        'id_team' => $team,
        'id_season' => $_SESSION['seasonId']
    ]);
    $val = $r->marketValue;
    return $val;
}


?>