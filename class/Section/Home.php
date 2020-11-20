<?php 
/**
 * 
 * Class Home
 * Manage Home page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use FootballPredictions\Theme;
use FootballPredictions\Menu\Menu;
use FootballPredictions\Menu\Submenu;

class Home
{
    public function __construct(){

    }
    
    static function menu(){
        echo Menu::menu();        
    }
    
    static function submenu($pdo, $form, $page, $create, $modify, $modifyuser ){
        // Display submenu with current button
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
                echo Submenu::menuAdmin($pdo, $form, $current);
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
    }
    
    static function unSet($page){
        // Unset SESSION values
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
}
?>