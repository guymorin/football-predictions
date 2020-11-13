<?php
/* This is the Football Predictions admin section page */
/* Author : Guy Morin */

// Namespaces
use FootballPredictions\Language;
use FootballPredictions\Theme;

?>

<h2><?= Theme::icon('admin') . " " . Language::title('administration');?></h2>

<?php
// Values
    if(($_SESSION['role'])!=2) header('Location:index.php');
    
    // DATABASE
    $val = '';
    $val .= "    <h3>" . ucfirst(Language::title('siteData')) . "</h3>\n";
    $dir    = 'data';
    
    $files = scandir($dir);
    $dumps = array();
    // Delete old files
    if(sizeof($files)>10) unlink($dir.'/'.$files[2]);
    foreach($files as $f){
        $last = substr($f,0,8);
        if(ctype_digit($last)) $dumps[] = $last;
    }
    sort($dumps);
    $last = end($dumps);
    // Red button if backup is one week old or more
    if($last!=''){
        $lastYear = substr($last,0,4);
        $lastMonth = substr($last,4,2);
        $lastDay = substr($last,6,2);
        $lastDump = $lastYear . '-' . $lastMonth . '-' . $lastDay;
        $time1 = mktime(0,0,0,$lastMonth,$lastDay,$lastYear);
        $time2 = time() - (7 * 24 * 60 * 60);
        $class = '';
        if($time1 < $time2) $class = 'red';
    } else {
        $class = 'red';
        $lastDump = '?';
    }

    $val .= "   <p><small>" . Language::title('lastSave') . " : " . $lastDump . "</small></p>\n";
    $val .= "   <form action='index.php?page=dump' method='POST'>\n";
    $val .= "            <button type='submit' class='".$class."'>". Language::title('save') . "</button>\n";
    $val .= "   </form>\n";
    
    
    // ACCOUNT   
    // List
    $val .= "    <h3>" . ucfirst(Language::title('account')) . "</h3>\n";
    $val .= "   <form action='index.php?page=accountList' method='POST'>\n";
    $val .= "            <button type='submit'>" . Language::title('listAccounts')  . "</button>\n";
    $val .= "   </form>\n";
    // Modify
    $req = "SELECT id_fp_user, name
            FROM fp_user
            ORDER BY name;";
    $data = $pdo->query($req);
    $counter = $pdo->rowCount();
    
    if($counter > 1){
        $val .= "<form action='index.php?page=account' method='POST'>\n";
        $val .= $form->inputAction('modifyuser');
        $val .= $form->label(Language::title('modifyAnAccount'));
        $val .= $form->selectSubmit('id_fp_user', $data);
        $val .= "</form>\n";
    }
    
    // SEASON
        $val .= "    <h3>" . ucfirst(Language::title('season')) . "</h3>\n";
        // Create
        $val .= "   <form action='index.php?page=season&create=1' method='POST'>\n";
        $val .= "            <button type='submit'> " . (Language::title('createASeason')) . "</button>\n";
        $val .= "   </form>\n";
        if(isset($_SESSION['seasonId'])){
            // Modify
            $req = "SELECT id_season, name FROM season ORDER BY name;";
            $data = $pdo->query($req);
            $counter = $pdo->rowCount();
            if($counter > 0){
                $val .= "<form action='index.php?page=season' method='POST'>\n";
                $val .= $form->inputAction('modify');
                $val .= $form->label(Language::title('modifyASeason'));
                $val .= $form->selectSubmit('id_season', $data);
                $val .= "</form>\n";
            }        
        }
    
    if(isset($_SESSION['championshipId'])){
        // CHAMPIONSHIP
        $val .= "   <h3>" . ucfirst(Language::title('championship')) . "</h3>\n";
        // Create
        $val .= "   <form action='index.php?page=championship&create=1' method='POST'>\n";

        $req = "SELECT DISTINCT id_team
                    FROM season_championship_team
                    WHERE id_season=" . $_SESSION['seasonId']."
                    AND id_championship=" . $_SESSION['championshipId'] . ";";
        $response = $pdo->query($req);
        $counter = $pdo->rowCount();
        if($counter>0){
            $_SESSION['noTeam'] = false;
            $val .= "            <button type='submit'>" . (Language::title('createAChampionship')) . "</button>\n";
        } else {
            $_SESSION['noTeam'] = true;
            $val .= "            <button type='submit'>". (Language::title('selectTheTeams')) . "</button>\n";
        }
        $val .= "   </form>\n";
        // Modify
        $req = "SELECT DISTINCT c.id_championship, c.name
                FROM championship c
                ORDER BY c.name;";
        $data = $pdo->query($req);
        $counter = $pdo->rowCount();
        if($counter > 1){
            $val .= "<form action='index.php?page=championship' method='POST'>\n";
            $val .= $form->inputAction('modify');
            $val .= $form->label(Language::title('modifyAChampionship'));
            $val .= $form->selectSubmit('id_championship', $data);
            $val .= "</form>\n";
        }
        
        // MATCHDAY
        $val .= "   <h3>" . ucfirst(Language::title('matchday')) . "</h3>\n";
        
        $req = "SELECT DISTINCT id_matchday, number
                FROM matchday
                WHERE id_season=" . $_SESSION['seasonId']."
                AND id_championship=" . $_SESSION['championshipId'] . " ORDER BY number DESC;";
        $response = $pdo->query($req);
        $counter = $pdo->rowCount();
        
        if($counter>0){
            $_SESSION['noMatchday'] = false;
            if(isset($_SESSION['matchdayId'])){
                $val .= "   <form action='/index.php?page=matchday&create=1' method='POST'>\n";
                $val .= "            <button type='submit'>" . (Language::title('createAMatchday')) . "</button>\n";
                $val .= "   </form>\n";
                if(($_SESSION['role'])==2){
                    $req = "SELECT DISTINCT id_matchday, number FROM matchday
                WHERE id_season = " . $_SESSION['seasonId'] . "
                AND id_championship = " . $_SESSION['championshipId'] . " ORDER BY number DESC;";
                    $data = $pdo->query($req);
                    $counter = $pdo->rowCount();
                    if($counter > 0){
                        $val .= "<form action='index.php?page=matchday' method='POST'>\n";
                        $val .= $form->inputAction('modify');
                        $val .= $form->label(Language::title('modifyAMatchday'));
                        $val .= $form->selectSubmit('matchdayModify', $data);
                        $val .= "</form>\n";
                    }
                }
            }
        } else {
            $_SESSION['noMatchday'] = true;
            $val .= "   <form action='/index.php?page=matchday&create=1' method='POST'>\n";
            $val .= "            <button type='submit'>" . (Language::title('createTheMatchdays')) . "</button>\n";
            $val .= "   </form>\n";
        }
    }

    // TEAM
    $val .= "   <h3>" . ucfirst(Language::title('team')) . "</h3>\n";
    $val .= "   <form action='/index.php?page=team&create=1' method='POST'>\n";
    $val .= "            <button type='submit'>" . (Language::title('createATeam')) . "</button>\n";
    $val .= "   </form>\n";
    
    if(($_SESSION['role'])==2){
        $req = "SELECT * FROM team c ORDER BY name;";
        $data = $pdo->query($req);
        $counter = $pdo->rowCount();
        if($counter > 1){
            $val .= "<form action='index.php?page=team' method='POST'>\n";
            $val .= $form->inputAction('modify');
            $val .= $form->label(Language::title('modifyATeam'));
            $val .= $form->selectSubmit('id_team', $data);
            $val .= "</form>\n";
        }
    }
    
    // PLAYER
    $val .= "   <h3>" . ucfirst(Language::title('player')) . "</h3>\n";
    $val .= "   <form action='/index.php?page=player&create=1' method='POST'>\n";
    $val .= "            <button type='submit'>" . (Language::title('createAPlayer')) . "</button>\n";
    $val .= "   </form>\n";
    
    if(($_SESSION['role'])==2){
        $req = "SELECT id_player, name, firstname
        FROM player
        ORDER BY name, firstname;";
        $data = $pdo->query($req);
        $counter = $pdo->rowCount();
        
        if($counter > 1){
            $val .= "<form action='index.php?page=player' method='POST'>\n";
            $val .= $form->inputAction('modify');
            $val .= $form->label(Language::title('modifyAPlayer'));
            $val .= $form->selectSubmit('id_player', $data);
            $val .= "</form>\n";
        }
    }

    echo $val;
?>
</section>