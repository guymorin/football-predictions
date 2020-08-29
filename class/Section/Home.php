<?php 
/**
 * 
 * Class Home
 * Manage Home page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Theme;

class Home
{
    public function __construct(){

    }
    
    static function submenu($pdo, $form, $page, $create, $modify, $modifyuser ){
        $val = '';
        $current = '';
        switch($page){
            case "account":
            case "accountList":
                if(isset($_SESSION['userLogin'])) {
                    if($page=='accountList') $current = 'listAccounts';
                    elseif ($modifyuser == 0) $current = 'myAccount';
                    echo Account::submenu($pdo, $form, $current);
                }
                break;
            case "championship":
            case "dashboard":
                if($create == 1)                $current = 'create';
                elseif($modify == 1 && $page == 'championship') $current = 'modify';
                elseif($page == 'championship') $current = 'standing';
                elseif($page=='dashboard')      $current = 'dashboard';
                echo Championship::submenu($pdo, $form, $current);
                break;
            case "matchday":
            case "matchgame":
            case "prediction":
            case "results":
            case "statistics":
            case "teamOfTheWeek":
                if($create == 1 && $page == 'matchday')         $current = 'create';
                elseif($modify == 1 && $page == 'matchday')     $current = 'modify';
                elseif($page == 'statistics')                   $current = 'statistics';
                elseif($page == 'matchday')                     $current = 'list';
                elseif($create == 1 && $page == 'matchgame')    $current = 'createMatch';
                elseif($page=='prediction')                     $current = 'prediction';
                elseif($page=='results')                        $current = 'results';
                elseif($page=='teamOfTheWeek')                  $current = 'teamOfTheWeek';
                echo Matchday::submenu($pdo, $form, $current);
                break;
            case "player":
                if($create == 1)        $current = 'create';
                elseif($modify == 1)    $current = 'modify';
                else                    $current = 'bestPlayers';
                echo Player::submenu($pdo, $form, $current);
                break;
            case "season":
                if($create == 1)        $current = 'create';
                elseif($modify == 1)    $current = 'modify';
                else                    $current = 'list';
                echo Season::submenu($pdo, $form, $current);
                break;
            case "team":
                if($create == 1 && $page == 'team')        $current = 'create';
                elseif($modify == 1 && $page == 'team')    $current = 'modify';
                else                                       $current = 'marketValue';
                echo Team::submenu($pdo, $form, $current);
                break;
            default:
                Account::exitButton();
                Season::exitButton();
                Championship::exitButton();
                Matchday::exitButton();
                break;
        }
        return $val;
    }
    
    static function homeMenu($pdo, $form){
        $val = '';
        $val .= "<ul class='menu'>\n";
        // DATABASE
        if($_SESSION['role'] == '2') {
            $val .= "    <li><h3>" . ucfirst(Language::title('siteData')) . "</h3>\n";
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
            $val .= "       <ul>\n";
            $val .= "            <li><a class='$class' href='index.php?page=dump'>" . (Language::title('save')) . "</a></li>\n";
            $val .= "       </ul>\n";
            $val .= "<br /><small>" . Language::title('lastSave') . " : " . $lastDump . "</small>\n";
        }
        /*
        // ACCOUNT
        $val .= "    <li><h3>" . Theme::icon('account') . " " . (Language::title('account')) . "</h3>\n";
        $val .= "       <ul>\n";
        if($_SESSION['role'] == '2') {
            $val .= "            <li><a href='index.php?page=accountList'>" . (Language::title('listAccounts')) . "</a></li>\n";
        }
        $val .= "            <li><a href='index.php?page=account'>" . (Language::title('myAccount')) . "</a></li>\n";
        $val .= "       </ul>\n";
        $val .= "    </li>\n";
        
        // SEASON
        $val .= "    <li><h3>" . Theme::icon('season') . " " . (Language::title('season')) . "</h3>\n";
        $val .= "       <ul>\n";
        $val .= "            <li><a href='index.php?page=season'>" . (Language::title('listChampionships')) . "</a></li>\n";
        $val .= "       </ul>\n";
        $val .= "    </li>\n";
        */
        // CHAMPIONSHIP
        $val .= "    <li><h3>" . Theme::icon('championship') . " " . (Language::title('championship')) . "</h3>\n";
        $req = "SELECT DISTINCT id_team  
            FROM season_championship_team  
            WHERE id_season=" . $_SESSION['seasonId']."
            AND id_championship=" . $_SESSION['championshipId'] . ";";
        $response = $pdo->query($req);
        $counter = $pdo->rowCount();
        if($counter>0){
            $_SESSION['noTeam'] = false;
            $val .= "       <ul>\n";
            $val .= "            <li><a href='index.php?page=championship'>" . (Language::title('standing')) . "</a></li>\n";
            $val .= "            <li><a href='index.php?page=dashboard'>" . (Language::title('dashboard')) . "</a></li>\n";
            $val .= "       </ul>\n";
        } else {
            $_SESSION['noTeam'] = true;
            $val .= "       <ul>\n";
            $val .= "            <li><a href='index.php?page=championship&create=1'>" . (Language::title('selectTheTeams')) . "</a></li>\n";
            $val .= "       </ul>\n";
        }
        $val .= "    </li>\n";
        
        // MATCHDAY
        $val .= "    <li><h3>" . Theme::icon('matchday') . " " . (Language::title('matchday')) . " " . (isset($_SESSION['matchdayNum']) ? $_SESSION['matchdayNum']:NULL)."</h3>\n";
                
        $val .= "        <ul>\n";
        $val .= "            <li><a href='index.php?page=matchday'>" . (Language::title('listMatchdays')) . "</a></li>\n";
        if(isset($_SESSION['matchdayId'])){
            $val .= "            <li><a href='index.php?page=statistics'>" . (Language::title('statistics')) . "</a></li>\n";
        /*
            $val .= "            <li><a href='index.php?page=prediction'>" . (Language::title('predictions')) . "</a></li>\n";
            $val .= "            <li><a href='index.php?page=results'>" . (Language::title('results')) . "</a></li>\n";
            $val .= "            <li><a href='index.php?page=teamOfTheWeek'>" . (Language::title('teamOfTheWeek')) . "</a></li>\n";
            $val .= "            <li><a href='index.php?page=matchgame&create=1'>" . (Language::title('createAMatch')) . "</a></li>\n";
        */
        } else {
            $req = "SELECT DISTINCT id_matchday, number
            FROM matchday
            WHERE id_season=" . $_SESSION['seasonId']."
            AND id_championship=" . $_SESSION['championshipId'] . " ORDER BY number DESC;";
            $response = $pdo->query($req);
            $counter = $pdo->rowCount();
            
            if($counter>0){
                $_SESSION['noMatchday'] = false;
                
                /*
                // Select form
                $list = "<form action='index.php?page=matchday' method='POST'>\n";
                $list .= $form->labelBr(Language::title('selectTheMatchday'));
                $list .= $form->selectSubmit("matchdaySelect", $response);
                $list .= "</form>\n";
                */
                
                // Quicknav button
                $req = "SELECT DISTINCT j.id_matchday, j.number FROM matchday j
                LEFT JOIN matchgame m ON m.id_matchday=j.id_matchday
                WHERE m.result IS NULL
                AND j.id_season=:id_season
                AND j.id_championship=:id_championship
                ORDER BY j.number;";
                $data = $pdo->prepare($req,[
                    'id_season' => $_SESSION['seasonId'],
                    'id_championship' => $_SESSION['championshipId']
                ]);
                $counter = $pdo->rowCount();
                if($counter>0){
                    $val .= "<br />\n";
                    $val .= "<form action='index.php?page=matchday' method='POST'>\n";
                    $val .=  $form->label(Language::title('quickNav'));
                    $val .=  $form->inputHidden("matchdaySelect", $data->id_matchday . "," . $data->number);
                    $val .=  $form->submit(Theme::icon('quicknav') . " " . (Language::title('MD')) . $data->number);
                    $val .= "</form>\n";
                    $val .= "<br />\n";
                }
                
                //$val .=  $list;                
            } else {
                $_SESSION['noMatchday'] = true;
                $val .= "            <li><a href='index.php?page=matchday&create=1'>" . (Language::title('createTheMatchdays')) . "</a></li>\n";
            }
        }
        $val .= "        </ul>\n";
        $val .= "    </li>\n";
        /*
        // TEAM
        $val .= "    <li><h3>" . Theme::icon('team') . " " . (Language::title('team')) . "</h3>\n";
        $val .= "        <ul>\n";
        $val .= "            <li><a href='index.php?page=team'>" . (Language::title('marketValue')) . "</a></li>\n";
        $val .= "            <li><a href='index.php?page=team&create=1'>" . (Language::title('createATeam')) . "</a></li>\n";
        $val .= "        </ul>\n";
        $val .= "    </li>\n";
        
        // PLAYER
        $val .= "    <li><h3>" . Theme::icon('player') . " " . (Language::title('player')) . "</h3>\n";
        $val .= "        <ul>\n";
        $val .= "            <li><a href='index.php?page=player'>" . (Language::title('bestPlayers')) . "</a></li>\n";
        $val .= "            <li><a href='index.php?page=player&create=1'>" . (Language::title('createAPlayer')) . "</a></li>\n";
        $val .= "        </ul>\n";
        $val .= "    </li>\n";
        $val .= "</ul>\n";
        */
        return $val;
    }
    
    static function unSet($page){
        switch($page){
            case "account":
                session_unset();
                session_destroy();
                break;
            case "season":
                unset($_SESSION['seasonId']);
                unset($_SESSION['seasonName']);
                unset($_SESSION['championshipId']);
                unset($_SESSION['championshipName']);
                unset($_SESSION['matchdayId']);
                unset($_SESSION['matchdayNum']);
                break;
            case "championship":
                unset($_SESSION['championshipId']);
                unset($_SESSION['championshipName']);
                unset($_SESSION['matchdayId']);
                unset($_SESSION['matchdayNum']);
                break;
            case "matchday":
                unset($_SESSION['matchdayId']);
                unset($_SESSION['matchdayNum']);
                break;
        }
    }
    static function menu(){
        $val = '';
        $val .= "  <input type='checkbox' id='fp-button' />\n";
        $val .= "  <label class='hamburger'  for='fp-button'>&#x2630;</label>\n";
        $val .= "  <div id='fp-menu'>\n";
        $val .= "  <ul>\n";
        $val .= "	 <li><a href='/'>" . (Language::title('homepage')) . "</a></li>\n";
        if(isset($_SESSION['userLogin'])){
            $val .= "	 <li><a href='index.php?page=account'>"
                            . Theme::icon('account') . " "
                            . (Language::title('account')) . "</a></li>\n";
            $val .= "	 <li><a href='index.php?page=season'>"
                            . Theme::icon('season') . " "
                            . (Language::title('season')) . "</a></li>\n";
            if(isset($_SESSION['seasonId'])){
                $val .= "	 <li><a href='index.php?page=championship'>"
                                . Theme::icon('championship') . " "
                                . (Language::title('championship')) . "</a></li>\n";
                if(isset($_SESSION['championshipId'])){
                    $val .= "	 <li><a href='index.php?page=matchday'>"
                                    . Theme::icon('matchday') . " "
                                    . (Language::title('matchday')) . " ".(isset($_SESSION['matchdayNum']) ? $_SESSION['matchdayNum']:NULL)."</a></li>\n";
                    $val .= "	 <li><a href='index.php?page=team'>"
                                    .Theme::icon('team') . " "
                                    . (Language::title('team')) . "</a></li>\n";
                    $val .= "	 <li><a href='index.php?page=player'>"
                                    .Theme::icon('player'). " "
                                    . (Language::title('player')) . "</a></li>\n";
                }
            }
        }
        $val .= "  </ul>\n";
        $val .= "  <label class='layer' for='fp-button'></label>\n";
        $val .= "  </div>\n";
        return $val;
    }
}
?>