<?php 
/**
 * 
 * Class Submenu
 * Manage Submenu page
 */
namespace FootballPredictions\Menu;
use FootballPredictions\Language;
use FootballPredictions\Theme;

class Submenu
{
    public function __construct(){

    }
    
    static function exitAccount() {
        if(isset($_SESSION['userLogin'])){
            echo "<a class='session' href='index.php?page=account&exit=1'>"
                .(Theme::icon('exit')) . " "
                    .(Language::title('logoff')) . " [" . ucfirst($_SESSION['userLogin'])."]</a>";
        }
    }
    
    static function exitSeason() {
        if(isset($_SESSION['seasonName'])){
            echo "<a class='session' href='index.php?page=season&exit=1'>"
                . (Theme::icon('season')) . " "
                    . (Language::title('selectTheSeason')) . "</a>";
        }
    }
    
    static function menuAccount($pdo, $form, $current = null){
        $currentClass = " class='current'";
        $classAL = $classMA = '';
        switch($current){
            case 'listAccounts':
                $classAL = $currentClass;
                break;
            case 'myAccount':
                $classMA = $currentClass;
                break;
        }
        $val = "<a" . $classMA . " href='index.php?page=account'>" . (Language::title('myAccount')) . "</a>";
        $val.= "<a href='index.php?page=season'>" . (Theme::icon('season')) . " " . (Language::title('season')) . "</a>";
        return $val;
    }
    
    static function menuAdmin($pdo, $form, $current = null){
        $val = '';
        Submenu::exitAccount();
        if(($_SESSION['role'])==2){
            $val .= "<a href='index.php?page=admin' class='current'>" . ucfirst(Language::title('administration')) . "</a>";
            $val .= "<a href='/'>" . (Theme::icon('soccer')) . " " . (Language::title('homepage')) . "</a>\n";
        }
        return $val;
    }
    
    static function menuChampionship($pdo, $form, $current=null){
        
        $val ='';
        $currentClass = " class='current'";
        $classS = $classDB = $classC = '';
        switch($current){
            case 'standing':
                $classS = $currentClass;
                break;
            case 'dashboard':
                $classDB = $currentClass;
                break;
            case 'create':
                $classC = $currentClass;
                break;
        }
        if(isset($_SESSION['championshipId']) && $_SESSION['championshipId']>0 &&
            (empty($_SESSION['noTeam']) or $_SESSION['noTeam'] == false)
            ){
                $val .= "  	<a href='index.php?page=season'>" . (Theme::icon('season')) . " " . $_SESSION['seasonName'] . "</a>";
                $val .= "<a" . $classDB . " href='index.php?page=dashboard'>" . (Language::title('dashboard')) . "</a>";
                $val .= "<a" . $classS . " href='index.php?page=championship'>" . (Language::title('standing')) . "</a>";
                $val .= "<a href='index.php?page=matchday'>" . (Theme::icon('matchday')) . " " . (Language::title('matchdays')) . "</a>";
        } else {
            if(isset($_SESSION['championshipId']) && $_SESSION['championshipId']>0) $val .= "  	<a href='/'>" . (Language::title('homepage')) . "</a>";
            else {
                Submenu::exitAccount();
                Submenu::exitSeason();
            }
        }
        return $val;
    }
    
    
    static function menuMatchday($pdo, $form, $current = null){
        $val = "<a href='index.php?page=dashboard'>" . (Theme::icon('championship')) . " ";
        if(isset($_SESSION['championshipId']) && $_SESSION['championshipId']>0) {
            $val .= $_SESSION['championshipName'];
        } else {
            $val .= Language::title('championship');
        }
        $val .= "</a>";
        $currentClass = " class='current'";
        $classS = $classP = $classR = $classTOTW = $classCM = $classLMD = $classCMD = '';
        switch($current){
            case 'statistics':
                $classS = $currentClass;
                break;
            case 'prediction':
                $classP = $currentClass;
                break;
            case 'results':
                $classR = $currentClass;
                break;
            case 'teamOfTheWeek':
                $classTOTW = $currentClass;
                break;
            case 'createMatch':
                $classCM = $currentClass;
                break;
            case 'create':
                $classCMD = $currentClass;
                break;
            case 'list':
                $classLMD = $currentClass;
                break;
        }
        if(isset($_SESSION['matchdayNum']) && $_SESSION['matchdayNum']>0){ 
            $val .= "<a" . $classLMD . " href='index.php?page=matchday'>" . (Language::title('listMatchdays')) . "</a>";
            $val .= "<a" . $classS . " href='index.php?page=statistics'>" . (Language::title('statistics')) . "</a>";
            if(($_SESSION['role'])==2){
                $val .= "<a" . $classP . " href='index.php?page=prediction'>" . (Language::title('predictions')) . "</a>";
                $val .= "<a" . $classR . " href='index.php?page=results'>" . (Language::title('results')) . "</a>";
                $val .= "<a" . $classTOTW . " href='index.php?page=teamOfTheWeek'>" . (Language::title('teamOfTheWeek')) . "</a>";
                $val .= "<a" . $classCM . " href='index.php?page=matchgame&create=1'>" . (Language::title('createAMatch')) . "</a>";
                $req = "SELECT DISTINCT mg.id_matchgame, t1.name, t2.name, mg.date
                FROM matchgame mg
                LEFT JOIN matchday md ON md.id_matchday = mg.id_matchday  
                LEFT JOIN team t1 ON mg.team_1 = t1.id_team 
                LEFT JOIN team t2 ON mg.team_2 = t2.id_team 
                WHERE md.id_season = " . $_SESSION['seasonId'] . "
                AND md.id_championship = " . $_SESSION['championshipId'] . " 
                AND md.id_matchday = " . $_SESSION['matchdayId'] . " ORDER BY mg.date;";
                $data = $pdo->query($req);
                $counter = $pdo->rowCount();
                if($counter > 0){
                    $val .= "<form action='index.php?page=matchgame' method='POST'>\n";
                    $val .= $form->inputAction('modify');
                    $val .= $form->label(Language::title('modifyAMatch'));
                    $val .= $form->selectSubmit('id_matchgame', $data);
                    $val .= "</form>\n";
                }
            }
            $val .= "<a href='index.php?page=team'>" . (Theme::icon('team')) . "&nbsp;" . (Language::title('teams')) . "</a>";
        } else {
            $val .= "<a" . $classLMD . " href='index.php?page=matchday'>" . (Language::title('listMatchdays')) . "</a>";
        }
        return $val;
    }
 
    
    static function menuPlayer($pdo, $form, $current = null){
        $val ='';
        $currentClass = " class='current'";
        $classBP = $classC = '';
        switch($current){
            case 'bestPlayers':
                $classBP = $currentClass;
                break;
            case 'create':
                $classC = $currentClass;
                break;
        }
        $response = $pdo->query("SELECT * FROM player ORDER BY name, firstname");
        $val .= "<a href='index.php?page=team'>" . (Theme::icon('team')) . "&nbsp;" . (Language::title('teams')) . "</a>";
        $val .= "<a" . $classBP . " href='index.php?page=player'>" . (Language::title('bestPlayers')) . "</a>";
        
        if(($_SESSION['role'])==2){
            $val .= "<a" . $classC . " href='index.php?page=player&create=1'>" . (Language::title('createAPlayer')) . "</a>";
            
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
        
        return $val;
    }
    
    static function menuPreferences($pdo, $form, $current = null){
        $val ='';
        $currentClass = " class='current'";
        $classP = $classP1 = '';
        switch($current){
            case 'preferences':
                $classP = $currentClass;
                break;
            case 'plugin_meteo_concept':
                $classP1 = $currentClass;
                break;
        }
        
        if(($_SESSION['role'])==2){
            $val .= "<a href='index.php?page=admin'>" . (Theme::icon('admin')) . "&nbsp;" . ucfirst(Language::title('administration')) . "</a>";
            $val .= "<a href='index.php?page=preferences'" . $classP . ">" . ucfirst(Language::title('preferences')) . "</a>";
            // Plugin
            $req = "SELECT plugin_name
                    FROM plugin
                    WHERE activate=1
                    AND plugin_name='meteo-concept';";
            $data = $pdo->prepare($req,[],true);
            $counter = $pdo->rowCount();
            if($counter==1){
                $val .= "<a href='index.php?page=plugin_meteo_concept' " . $classP1 . ">" . ucfirst('Meteo-Concept') . "</a>";
            }
        
        }
        return $val;
    }
    
    static function menuSeason($pdo, $form, $current=null){
        $val ='';
        $currentClass = " class='current'";
        $classL = $classC = '';
        switch($current){
            case 'list':
                $classL = $currentClass;
                break;
            case 'create':
                $classC = $currentClass;
                break;
        }
        if(isset($_SESSION['seasonId']) && $_SESSION['seasonId']>0) {
            Submenu::exitSeason();
            $val .= "<a" . $classL . " href='index.php?page=season'>" . (Language::title('listChampionships')) . "</a>";
            if(isset($_SESSION['championshipId'])){
                $val .= "<a href='index.php?page=dashboard'>" . (Theme::icon('championship')) . " ";
                if($_SESSION['championshipId']>0) {
                    $val .= $_SESSION['championshipName'];
                } else {
                    $val .= Language::title('championship');
                }
                $val .= "</a>";
            }
        } else Submenu::exitAccount();
        return $val;
    }
    
    static function menuTeam($pdo, $form, $current=null){
        $val ='';
        $currentClass = " class='current'";
        $classMV = $classC = '';
        switch($current){
            case 'marketValue':
                $classMV = $currentClass;
                break;
            case 'create':
                $classC = $currentClass;
                break;
            case '':break;
        }
        $val .= "  	<a href='index.php?page=matchday'>" . (Theme::icon('matchday')) . " " . (Language::title('matchdays')) . "</a>";
        $val .= "<a" . $classMV . " href='index.php?page=team'>" . (Language::title('marketValue')) . "</a>";
        if(($_SESSION['role'])==2){
            $val .= "<a" . $classC . " href='index.php?page=team&create=1'>" . (Language::title('createATeam')) . "</a>\n";
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
        $val .= "<a href='index.php?page=player'>" . (Theme::icon('player')) . " " . (Language::title('player')) . "</a>";
        return $val;
    }
    
}
?>
