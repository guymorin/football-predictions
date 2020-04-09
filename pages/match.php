<?php
/* This is the Football Predictions match section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\Errors;
use FootballPredictions\Forms;
use FootballPredictions\Section\Matchday;


// Values

// No matchday selected
if(empty($_SESSION['matchdayId'])){

    // Popup matchday
    if(isset($_POST['matchdaySelect'])){
        $v=explode(",",$_POST['matchdaySelect']);
        $_SESSION['matchdayId']=$error->check("Digit",$v[0]);
        $_SESSION['matchdayNum']=$error->check("Digit",$v[1]);
        popup($title_MD.$_SESSION['matchdayNum'],"index.php?page=match");
    }
}
// Matchday selected
else {
    echo "<h2>$icon_matchday $title_matchday ".$_SESSION['matchdayNum']."</h2>\n";
    changeMD($pdo,"match&create=1");
    // Values
    $date = $result = "";
    $idMatch = $team1 = $team2 = $odds1 = $oddsD = $odds2 = 0;
    isset($_POST['id_matchgame'])   ? $idMatch=$error->check("Digit",$_POST['id_matchgame']) : null;
    isset($_POST['team_1'])         ? $team1=$error->check("Digit",$_POST['team_1']) : null;
    isset($_POST['team_2'])         ? $team2=$error->check("Digit",$_POST['team_2']) : null;
    isset($_POST['result'])         ? $result=$error->check("Result",$_POST['result']) : null;
    isset($_POST['odds1'])          ? $odds1=$error->check("Digit",$_POST['odds1']) : null;
    isset($_POST['oddsD'])          ? $oddsD=$error->check("Digit",$_POST['oddsD']) : null;
    isset($_POST['odds2'])          ? $odds2=$error->check("Digit",$_POST['odds2']) : null;
    isset($_POST['date'])           ? $date=$error->check("Digit",$_POST['date']) : null;
    $create = $modify = $delete = 0;
    isset($_GET['create'])          ? $create=$error->check("Action",$_GET['create']) : null;
    if($create==0) isset($_POST['create'])     ? $create=$error->check("Action",$_POST['create']) : null;
    isset($_GET['modify'])          ? $modify=$error->check("Action",$_GET['modify']) : null;
    if($modify==0) isset($_POST['modify'])     ? $modify=$error->check("Action",$_POST['modify']) : null;
    isset($_POST['delete'])         ? $delete=$error->check("Action",$_POST['delete']) : null;

// Popup if needed
    // Delete
    if($delete==1){
            $req="DELETE FROM matchgame WHERE id_match=:id_match;";
            $pdo->prepare($req,[
                'id_match' => $idMatch
            ]);
            $pdo->alterAuto('matchgame');
            popup($title_deleted,"index.php?page=match");
    }
    // Create
    elseif($create==1){

        echo "<h3>$title_createAMatch</h3>\n";
        // Create popup
        if(($team1>0)&&($team2>0)&&($team1!=$team2)){
            $pdo->alterAuto('matchgame');
            $req="INSERT INTO matchgame 
            VALUES(NULL,'".$_SESSION['matchdayId']."',:team1,:team2,:result,:odds1,:oddsD,:odds2,:date,0,0,0,0);";
            $pdo->prepare($req,[
                $_SESSION['matchdayId'],
                'team1' => $team1,
                'team2' => $team2,
                'result' => $result,
                'odds1' => $odds1,
                'oddsD' => $oddsD,
                'odds2' => $odds2,
                'date' => $date
            ]);
            popup($title_created,"index.php?page=match&create=1");
        }
        // Create form 
        else {            
            echo Matchday::createMatchForm($pdo, $error, $form);
    	}
    }
    // Modify
    else {
        echo "<h3>$title_modifyAMatch</h3>\n";
        // Modify popup
        if(($team1>0)&&($team2>0)&&($team1!=$team2)){
            $req="UPDATE matchgame 
            SET id_matchday = :id_matchday, team_1=:team_1, team_2 = :team_2, result = :result 
            WHERE id_match = :id_match;";
            $pdo->prepare($req,[
                'id_matchday' => $_SESSION['matchdayId'],
                'team_1' => $team1,
                'team_2' => $team2,
                'result' => $result,
                'id_match' => $idMatch
            ]);
            popup($title_modifyAMatch,"index.php?page=match");
        } 
        // Modify form
        elseif($idMatch>0){
            $req="SELECT m.id_matchgame,c1.name as name1,c2.name as name2,c1.id_team as id1,c2.id_team as id2, m.result, m.date, m.odds1, m.oddsD, m.odds2 
            FROM matchgame m LEFT JOIN team c1 ON m.team_1=c1.id_team LEFT JOIN team c2 ON m.team_2=c2.id_team 
            WHERE m.id_matchgame = :id_matchgame;";
            $data = $pdo->prepare($req,[
                'id_matchgame' => $idMatch
            ]);
            
            echo Matchday::modifyMatchForm($pdo, $data, $idMatch, $error, $form);

        }
        // Modify selection of a match
        else {

            echo $error->getError();
            echo "<form action='index.php?page=match' method='POST'>\n";
            echo $form->inputAction('modify'); 
            
            echo "      <label>$title_modifyAMatch :</label>\n";                                    
            echo "  	<select multiple size='10' name='id_matchgame'>\n";
            $req = "SELECT m.id_matchgame,c1.name as name1,c2.name as name2, m.result 
            FROM matchgame m LEFT JOIN team c1 ON m.team_1=c1.id_team 
            LEFT JOIN team c2 ON m.team_2=c2.id_team 
            WHERE m.id_matchday = :id_matchday;";
            $data= $db->prepare($req,[
                'id_matchday' => $_SESSION['matchdayId']
                ]);
            foreach ($data as $d)
            {
                echo "  		<option value='".$d->id_matchgame."'>";
                if($d->result!=""){
                    if($d->result=="D") echo "[$title_draw] ";
                    else echo "[".$d->result."] ";
                }
                echo $d->name1." - ".$d->name2."</option>\n";
            }
            echo "	    </select>\n";
            echo "<br />\n";
            
            echo $form->submit($title_select);
            echo " </form>\n";
            
        }
    }

}
?>  
