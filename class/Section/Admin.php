<?php 
/**
 * 
 * Class Admin
 * Manage Admin page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Theme;
use FootballPredictions\Menu\Menu;
use FootballPredictions\Menu\Submenu;

class Admin
{
    public function __construct(){

    }
    
    private static function surround($html){
        $val = '';
        $val .= "<tr>\n";
        $val .= $html;
        $val .= "</tr>\n";
        return $val;
    }
    
    
    static function accountList($pdo,$form){
        $val = '';
        // Title
        $val .= "   <td>" . ucfirst(Language::title('account')) . "</td>\n";
        // List
        $req = "SELECT id_fp_user, name
            FROM fp_user
            ORDER BY name;";
        $data = $pdo->query($req);
        $counter = $pdo->rowCount();
        $val .= "   <td>\n";
        $val .= "       <form action='index.php?page=accountList' method='POST'>\n";
        $val .= "            <button type='submit'>" . Theme::icon('account'). " " . Language::title('listAccounts')  . "</button>\n";
        $val .= "       </form>\n";
        $val .= "  </td>\n";
        // Modify
        if($counter > 1){
            $val .= "  <td>\n";
            $val .= "       <form action='index.php?page=account' method='POST'>\n";
            $val .= $form->inputAction('modifyuser');
            $val .= $form->label(Language::title('modifyAnAccount'));
            $val .= "  </td>\n";
            $val .= "  <td>\n";
            $val .= $form->selectSubmit('id_fp_user', $data);
            $val .= "       </form>\n";
            $val .= "  </td>\n";
        } else {
            $val .= "  <td colspan='2'></td>\n";
        }
        $val = Admin::surround($val);
        return $val;
    }
    
    static function adminButton(){
        $val = '';
        if(($_SESSION['role'])==2){
            $val .= "   <br />\n<form action='index.php?page=admin' method='POST'>\n";
            $val .= "            <button type='submit'> "
                .Theme::icon('admin')." "
                    . ucfirst(Language::title('administration')) . "</button>\n";
                    $val .= "   </form>\n";
        }
        return $val;
    }
    
    static function championshipList($pdo,$form) {
        $val = '';
        if(isset($_SESSION['championshipId'])){
            
            // Title
            $val .= "   <td>" . ucfirst(Language::title('championship')) . "</td>\n";
            // Create or select
            $val .= "   <td>\n";
            $val .= "       <form action='index.php?page=championship&create=1' method='POST'>\n";
            $req = "SELECT DISTINCT id_team
                    FROM season_championship_team
                    WHERE id_season=" . $_SESSION['seasonId']."
                    AND id_championship=" . $_SESSION['championshipId'] . ";";
            $response = $pdo->query($req);
            $counter = $pdo->rowCount();
            $val .= "            <button type='submit'>" . Theme::icon('create'). " ";
            if($counter>0){
                $_SESSION['noTeam'] = false;
                $val .= Language::title('createAChampionship');
            } else {
                $_SESSION['noTeam'] = true;
                $val .= Language::title('selectTheTeams');
            }
            $val .= "</button>\n";
            $val .= "      </form>\n";
            $val .= "   </td>\n";
            // Modify
            $req = "SELECT DISTINCT c.id_championship, c.name
                FROM championship c
                ORDER BY c.name;";
            $data = $pdo->query($req);
            $counter = $pdo->rowCount();
            if($counter > 1){    
                $val .= "   <td>\n";
                $val .= "   <form action='index.php?page=championship' method='POST'>\n";
                $val .= $form->inputAction('modify');
                $val .= $form->label(Language::title('modifyAChampionship'));
                $val .= "   </td>\n";
                $val .= "   <td>\n";
                $val .= $form->selectSubmit('id_championship', $data);
                $val .= "   </form>\n";
                $val .= "   </td>\n";
            } else {
                $val .= "  <td colspan='2'></td>\n";
            }
            $val = Admin::surround($val);
        
        }
        return $val;
    }
    
    static function matchdayList($pdo,$form){
        $val = '';
        if(isset($_SESSION['championshipId'])){
            $req = "SELECT DISTINCT id_matchday, number
                    FROM matchday
                    WHERE id_season=" . $_SESSION['seasonId']."
                    AND id_championship=" . $_SESSION['championshipId'] . " ORDER BY number DESC;";
            $response = $pdo->query($req);
            $counter = $pdo->rowCount();
            if($counter>0){
                $_SESSION['noMatchday'] = false;
                if(isset($_SESSION['matchdayId'])){
                    $val .= "   <td>" . ucfirst(Language::title('matchday')) . "</td>\n";
                    $val .= "   <td>\n";
                    $val .= "   <form action='/index.php?page=matchday&create=1' method='POST'>\n";
                    $val .= "            <button type='submit'>" 
                            . Theme::icon('create') . " "  
                            . (Language::title('createAMatchday')) 
                            . "</button>\n";
                    $val .= "   </form>\n";
                    $val .= "   </td>\n";
                    $req = "SELECT DISTINCT id_matchday, number FROM matchday
                    WHERE id_season = " . $_SESSION['seasonId'] . "
                    AND id_championship = " . $_SESSION['championshipId'] . " ORDER BY number DESC;";
                    $data = $pdo->query($req);
                    $counter = $pdo->rowCount();
                    if($_SESSION['role']==2 and $counter > 0){
                            $val .= "   <td>\n";
                            $val .= "<form action='index.php?page=matchday' method='POST'>\n";
                            $val .= $form->inputAction('modify');
                            $val .= $form->label(Language::title('modifyAMatchday'));
                            $val .= "   </td>\n";
                            $val .= "   <td>\n";
                            $val .= $form->selectSubmit('matchdayModify', $data);
                            $val .= "</form>\n";
                    } else {
                        $val .= "   <td colspan='2'></td>\n";
                    }
                    $val = Admin::surround($val);
                }
            } else {
                $_SESSION['noMatchday'] = true;
                $val .= "   <td>" . ucfirst(Language::title('matchday')) . "</td>\n";
                $val .= "   <td>\n";
                $val .= "   <form action='/index.php?page=matchday&create=1' method='POST'>\n";
                $val .= "            <button type='submit'>" 
                            . Theme::icon('create') . " "  
                            . (Language::title('createTheMatchdays')) 
                            . "</button>\n";
                $val .= "   </form>\n";
                $val .= "   </td>\n";
                $val .= "   <td colspan='2'></td>\n";
                $val = Admin::surround($val);
            }
        }
        return $val;
    }
    
    static function playerList($pdo,$form) {
        $val = '';
        if(isset($_SESSION['championshipId'])){
            $val .= "   <td>" . ucfirst(Language::title('player')) . "</td>\n";
            $val .= "   <td>\n";
            $val .= "   <form action='/index.php?page=player&create=1' method='POST'>\n";
            $val .= "            <button type='submit'>" . Theme::icon('create') . " " 
                        . (Language::title('createAPlayer')) 
                        . "</button>\n";
            $val .= "   </form>\n";
            $val .= "   </td>\n";
            $val .= "   <td>\n";
            $req = "SELECT id_player, name, firstname
            FROM player
            ORDER BY name, firstname;";
            $data = $pdo->query($req);
            $counter = $pdo->rowCount();
            
            if($_SESSION['role']==2 and $counter > 1){
                $val .= "       <form action='index.php?page=player' method='POST'>\n";
                $val .= $form->inputAction('modify');
                $val .= $form->label(Language::title('modifyAPlayer'));
                $val .= "   </td>\n";
                $val .= "   <td>\n";
                $val .= $form->selectSubmit('id_player', $data);
                $val .= "       </form>\n";
            } else {
                $val .= "   </td>\n";
                $val .= "   <td>\n";
            }
            $val .= "   </td>\n";
            $val = Admin::surround($val);
        }
        return $val;
    }

    static function preferences() {
        $val = '';
        if(($_SESSION['role'])==2){
        $val .= "   <td>" . ucfirst(Language::title('preferences')) . "</td>\n";
        $val .= "   <td>\n";
        $val .= "   <form action='/index.php?page=preferences' method='POST'>\n";
        $val .= "            <button type='submit'>" . Theme::icon('preferences') . " "
            . (Language::title('Preferences'))
            . "</button>\n";
            $val .= "   </form>\n";
            $val .= "   </td>\n";
            $val .= "   <td colspan='2'></td>\n";
            $val = Admin::surround($val);
        }
        return $val;
    }
    
    static function seasonList($pdo,$form){
        $val = '';
        // Title
        $val .= "   <td>" . ucfirst(Language::title('season')) . "</td>\n";
        // Create
        $val .= "   <td>\n";
        $val .= "       <form action='index.php?page=season&create=1' method='POST'>\n";
        $val .= "            <button type='submit'> " 
                        . Theme::icon('create') . " " 
                        . Language::title('createASeason') 
                        . "</button>\n";
        $val .= "       </form>\n";
        $val .= "   </td>\n";
        // Modify
        if(isset($_SESSION['seasonId'])){
            $req = "SELECT id_season, name FROM season ORDER BY name;";
            $data = $pdo->query($req);
            $counter = $pdo->rowCount();
            $val .= "   <td>\n";
            if($counter > 0){
                $val .= "<form action='index.php?page=season' method='POST'>\n";
                $val .= $form->inputAction('modify');
                $val .= $form->label(Language::title('modifyASeason'));
                $val .= "   </td>\n";
                $val .= "   <td>\n";
                $val .= $form->selectSubmit('id_season', $data);
                $val .= "</form>\n";
            } else {
                $val .= "   </td>\n";
                $val .= "   <td>\n";
            }
            $val .= "   </td>\n";
        } else {
            $val .= "   <td colspan='2'></td>\n";
        }
        
        $val = Admin::surround($val);
        return $val;
    }
    
    static function siteData(){
        $val = '';
        
        // Scan directory
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
        // Change button if backup is at least one week old
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
        // Title
        $val .= "   <td>" . ucfirst(Language::title('siteData')) . "</td>\n";
        // Save
        $val .= "   <td><form action='index.php?page=dump' method='POST'>\n";
        $val .= "            <button type='submit' class='".$class."'>"
                    . Theme::icon('floppyDisk') . " "
                    . Language::title('save') . "</button>\n";
        $val .= "   </form></td>\n";
        // Text
        $val .= "   <td colspan='2'>" . Language::title('lastSave') . " : " . $lastDump . "</td>\n";
        $val = Admin::surround($val);
        return $val;
    }
    
    static function teamList($pdo,$form) {
        $val = '';
        
        $val .= "   <td>" . ucfirst(Language::title('team')) . "</td>\n";
        $val .= "   <td>\n";
        $val .= "       <form action='/index.php?page=team&create=1' method='POST'>\n";
        $val .= "            <button type='submit'>" 
                        . Theme::icon('create') . " "  
                        . (Language::title('createATeam')) 
                        . "</button>\n";
        $val .= "       </form>\n";
        $val .= "   </td>\n";
        $val .= "   <td>\n";
        if(($_SESSION['role'])==2){
            $req = "SELECT * FROM team c ORDER BY name;";
            $data = $pdo->query($req);
            $counter = $pdo->rowCount();
            if($counter > 1){
                $val .= "<form action='index.php?page=team' method='POST'>\n";
                $val .= $form->inputAction('modify');
                $val .= $form->label(Language::title('modifyATeam'));
                $val .= "   </td>\n";
                $val .= "   <td>\n";
                $val .= $form->selectSubmit('id_team', $data);
                $val .= "</form>\n";
            }
        } else {
            $val .= "   </td>\n";
            $val .= "   <td>\n";
        }
        $val .= "   </td>\n";
        $val = Admin::surround($val);
        return $val;
    }
}
?>