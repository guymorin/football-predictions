<?php
/* This is the Football Predictions team of the week section page */
/* Author : Guy Morin */

// Files to include
use FootballPredictions\Language;
use FootballPredictions\Theme;


echo "<h2>" . Theme::icon('matchday') . " " . (Language::title('matchday')) . " " . $_SESSION['matchdayNum']."</h2>\n";

// Values
$teamOfTheWeek = 0;
isset($_POST['teamOfTheWeek']) ? $teamOfTheWeek=$error->check("Action",$_POST['teamOfTheWeek']) : null;
isset($_POST['deletePlayer']) ? $deletePlayer = $_POST['deletePlayer'] : $deletePlayer = array();
isset($_POST['id_player']) ? $idPlayer = $_POST['id_player'] : $idPlayer = array();
isset($_POST['rating']) ? $ratingPlayer = $_POST['rating'] : $ratingPlayer = array();

$val = array_combine($idPlayer,$ratingPlayer);

// Only if a matchday is selected
if(isset($_SESSION['matchdayId'])){

    changeMD($pdo,"teamOfTheWeek");
    
    // Popup modified
    if($teamOfTheWeek==1){
        $pdo->alterAuto('teamOfTheWeek');
        $req="";
        if(sizeof($deletePlayer)>0){
            foreach($deletePlayer as $d){
                $r1="DELETE FROM teamOfTheWeek WHERE id_matchday='".$_SESSION['matchdayId']."' AND id_player='".$d."';";
                $pdo->exec($r1);
            }
            $pdo->alterAuto('teamOfTheWeek');
        }
        $req="";
        foreach($val as $k=>$v){
            $v=$error->check("Digit",$v);
            if(($v>0)&&(!in_array($k,$deletePlayer))){
                $r2 = "SELECT COUNT(*) as nb FROM teamOfTheWeek 
                WHERE id_matchday = :id_matchday 
                AND id_player = :id_player;";
                $data = $pdo->prepare($r2,[
                    'id_matchday' => $_SESSION['matchdayId'],
                    'id_player' => $k
                ]);
                if($data->nb==0){
                    $req.="INSERT INTO teamOfTheWeek VALUES(NULL,'".$_SESSION['matchdayId']."','".$k."','".$v."');";
                } else {
                    $req.="UPDATE teamOfTheWeek SET rating='".$v."' WHERE id_matchday='".$_SESSION['matchdayId']."' AND id_player='".$k."';";
                }
                
                $teamPlayerId = 0;
                $r3 = "SELECT c.id_team
                FROM player j
                LEFT JOIN season_team_player scj ON scj.id_player=j.id_player
                LEFT JOIN season_championship_team scc ON scc.id_team=scj.id_team
                LEFT JOIN team c ON c.id_team=scj.id_team 
                WHERE scc.id_season = :id_season 
                AND scc.id_championship = :id_championship 
                AND j.id_player = :id_player;";
                $data = $pdo->prepare($r3,[
                    'id_season' => $_SESSION['seasonId'],
                    'id_championship' => $_SESSION['championshipId'],
                    'id_player' => $k
                ]);
                if($data->id_team!=null){
                    $teamPlayerId = $data->id_team;
                                
                    $r4 = "SELECT COUNT(*) as nb 
                    FROM season_team_player stp 
                    WHERE id_season = :id_season
                    AND id_player = :id_player;";
                    $data = $pdo->prepare($r4,[
                        'id_season' => $_SESSION['seasonId'],
                        'id_player' => $k
                    ]);
                    if($data->nb==0){
                        $req.="INSERT INTO season_team_player VALUES(NULL,'".$_SESSION['seasonId']."','".$teamPlayerId."','".$k."');";
                    }
                }
            }
        }
        $pdo->exec($req);
        popup(Language::title('modified'),"index.php?page=teamOfTheWeek");
    }

    // Modify form
    else {
        echo "<h3>" . (Language::title('teamOfTheWeek')) . "</h3>\n";
        echo "<p><a href='/index.php?page=player&create=1'>" . (Language::title('createAPlayer')) . " ?</a></p>";
        echo "<table id='teamOfTheWeek'>\n";
        echo "  <tr>\n";
        echo "      <th> </th>\n";
        echo "      <th>" . (Language::title('player')) . "</th>\n";
        echo "      <th>" . (Language::title('rating')) . "</th>\n";
        echo "      <th>&#10060;</th>\n";
        echo "  </tr>\n";
        
        $counter=0;
        $req = "SELECT j.id_player,j.name,j.firstname,e.rating
        FROM teamOfTheWeek e
        LEFT JOIN player j ON e.id_player=j.id_player
        WHERE id_matchday=:id_matchday
        ORDER BY j.position,j.name,j.firstname;";
        $data = $pdo->prepare($req,[
            'id_matchday' => $_SESSION['matchdayId']
        ],true);
        
        echo $error->getError();
        echo "<form action='index.php?page=teamOfTheWeek' method='POST' onsubmit='return confirm();'>\n";
        $form->setValues($data);
        echo $form->inputAction('teamOfTheWeek');   
        foreach ($data as $d)
        {
            $counter++;
            echo "  <tr>";
            echo "      <td>".$counter."</td>\n";
            echo "      <td>";
            echo $form->inputHidden('id_player[]',$d->id_player);
            echo mb_strtoupper($d->name,'UTF-8')." ".$d->firstname;
            echo "</td>\n";
            $form->setValue('rating',$d->rating);
            echo "      <td>" . $form->input('','rating[]') . "</td>\n";
            echo "      <td><input type='checkbox' name='deletePlayer[]' value='".$d->id_player."'>";
            echo "</td>\n";
            echo "  </tr>\n";
        }
        
        $req = "SELECT j.id_player, j.name, j.firstname, j.position, c.name as team 
        FROM player j
        LEFT JOIN season_team_player scj ON scj.id_player=j.id_player 
        LEFT JOIN season_championship_team scc ON scc.id_team=scj.id_team 
        LEFT JOIN team c ON c.id_team=scj.id_team
        WHERE scc.id_season = :id_season 
        AND scc.id_championship = :id_championship 
        ORDER BY j.name, j.firstname;";
       
        $playersLeft=11-$counter;
        for($i=0;$i<$playersLeft;$i++){
            $counter++;
            $data = $pdo->prepare($req,[
                'id_season' => $_SESSION['seasonId'],
                'id_championship' => $_SESSION['championshipId']
            ]);
            echo " <tr>\n";
            echo "  <td>".$counter."</td>\n";
            echo "  <td>" . $form->selectPlayer($pdo,'id_player[]') . "</td>\n";
            echo "  <td><p><input maxlength='50' type='text' name='rating[]' value=''></p></td>\n";
            echo "  <td> </td>\n";
            echo "</tr>\n";
        }
        echo "</table>\n";
        echo $form->submit(Language::title('modify'));
        echo "<form>\n";   
    }
}