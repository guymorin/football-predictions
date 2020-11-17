<?php 
/**
 * 
 * Class Home
 * Manage Home page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Theme;
use FootballPredictions\Menu\Submenu;

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
                Submenu::exitAccount();
                if(isset($_SESSION['userLogin'])) {
                    if($page=='accountList') $current = 'listAccounts';
                    elseif ($modifyuser == 0) $current = 'myAccount';
                    echo Submenu::menuAccount($pdo, $form, $current);
                }
                break;
            case "admin":
                Submenu::exitAccount();
                $val .= "<a href='index.php?page=admin' class='current'>" . ucfirst(Language::title('administration')) . "</a>";
                $val .= "<a href='/'>" . (Language::title('homepage')) . "</a>\n";
                break;
            case "championship":
            case "dashboard":
                if($create == 1)                $current = 'create';
                elseif($modify == 1 && $page == 'championship') $current = 'modify';
                elseif($page == 'championship') $current = 'standing';
                elseif($page=='dashboard')      $current = 'dashboard';
                echo Submenu::menuChampionship($pdo, $form, $current);
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
                echo Submenu::menuMatchday($pdo, $form, $current);
                break;
            case "player":
                if($create == 1)        $current = 'create';
                elseif($modify == 1)    $current = 'modify';
                else                    $current = 'bestPlayers';
                echo Submenu::menuPlayer($pdo, $form, $current);
                break;
            case "season":
                if($create == 1)        $current = 'create';
                elseif($modify == 1)    $current = 'modify';
                else                    $current = 'list';
                echo Submenu::menuSeason($pdo, $form, $current);
                break;
            case "team":
                if($create == 1 && $page == 'team')        $current = 'create';
                elseif($modify == 1 && $page == 'team')    $current = 'modify';
                else                                       $current = 'marketValue';
                echo Submenu::menuTeam($pdo, $form, $current);
                break;
            default:
                Submenu::exitAccount();
                break;
        }
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
            $val .= "	 <li><a href='/index.php?page=account&exit=1'>" 
                . Theme::icon('exit') . " "
                . (Language::title('logoff')) . "</a></li>\n";
            
            // Admin page link
            if(($_SESSION['role'])==2){
                $val .= "	 <li><a href='index.php?page=admin'>"
                            . Theme::icon('admin') . " "
                            . (Language::title('administration')) . "</a></li>\n";
            }
            
            $val .= "	 <li><a href='index.php?page=account'>"
                            . Theme::icon('account') . " "
                            . (Language::title('account')) . "</a></li>\n";
            $val .= "	 <li><a href='index.php?page=season'>"
                            . Theme::icon('season') . " "
                            . (Language::title('season')) . "</a></li>\n";
            if(isset($_SESSION['seasonId']) and isset($_SESSION['championshipId'])){
                    
                    $val .= "	 <li><a href='index.php?page=dashboard'>"
                                . Theme::icon('championship') . " "
                                . (Language::title('championship')) . "</a></li>\n";
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
        $val .= "  </ul>\n";
        $val .= "  <label class='layer' for='fp-button'></label>\n";
        $val .= "  </div>\n";
        return $val;
    }
}
?>