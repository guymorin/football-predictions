<?php 
/**
 * 
 * Class Home
 * Manage Home page
 */
namespace FootballPredictions\Section;
use FootballPredictions\Language;
use \PDO;

class Home
{
    public function __construct(){

    }
    
    static function submenu($pdo, $form, $page, $create, $modify ){
        $val = '';
        $current = '';
        switch($page){
            case "account":
                if(isset($_SESSION['userLogin'])) {
                    $current = 'myAccount';
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
        require '../theme/default/theme.php';
        $val = '';
        $val .= "<ul class='menu'>\n";
        $val .= "    <li><h2>$icon_account " . (Language::title('account')) . "</h2>\n";
        $val .= "       <ul>\n";
        $val .= "            <li><a href='index.php?page=account'>" . (Language::title('myAccount')) . "</a></li>\n";
        $val .= "       </ul>\n";
        $val .= "    </li>\n";
        $val .= "    <li><h2>$icon_season " . (Language::title('season')) . "</h2>\n";
        $val .= "       <ul>\n";
        $val .= "            <li><a href='index.php?page=season'>" . (Language::title('listChampionships')) . "</a></li>\n";
        $val .= "       </ul>\n";
        $val .= "    </li>\n";
        
        $req = "SELECT DISTINCT id_team  
            FROM season_championship_team  
            WHERE id_season=" . $_SESSION['seasonId']."
            AND id_championship=" . $_SESSION['championshipId'] . ";";
        $response = $pdo->query($req);
        $counter = $pdo->rowCount();

        $val .= "    <li><h2>$icon_championship " . (Language::title('championship')) . "</h2>\n";
        if($counter>0){
            $_SESSION['noMatchday'] = false;
            $val .= "       <ul>\n";
            $val .= "            <li><a href='index.php?page=championship'>" . (Language::title('standing')) . "</a></li>\n";
            $val .= "            <li><a href='index.php?page=dashboard'>" . (Language::title('dashboard')) . "</a></li>\n";
            $val .= "       </ul>\n";
        } else {
            $_SESSION['noMatchday'] = true;
            $val .= "       <ul>\n";
            $val .= "            <li><a href='index.php?page=championship&create=1'>" . (Language::title('selectTheTeams')) . "</a></li>\n";
            $val .= "       </ul>\n";
        }
        $val .= "    </li>\n";
        
        $val .= "    <li><h2>$icon_matchday " . (Language::title('matchday')) . " " . (isset($_SESSION['matchdayNum']) ? $_SESSION['matchdayNum']:NULL)."</h2>\n";
        if(isset($_SESSION['matchdayId'])){
            $val .= "        <ul>\n";
            $val .= "            <li><a href='index.php?page=statistics'>" . (Language::title('statistics')) . "</a></li>\n";
            $val .= "            <li><a href='index.php?page=prediction'>" . (Language::title('predictions')) . "</a></li>\n";
            $val .= "            <li><a href='index.php?page=results'>" . (Language::title('results')) . "</a></li>\n";
            $val .= "            <li><a href='index.php?page=teamOfTheWeek'>" . (Language::title('teamOfTheWeek')) . "</a></li>\n";
            $val .= "        </ul>\n";
        } else {
            $val .= "        <ul>\n";
            
            $req = "SELECT DISTINCT id_matchday, number
            FROM matchday
            WHERE id_season=" . $_SESSION['seasonId']."
            AND id_championship=" . $_SESSION['championshipId'] . " ORDER BY number DESC;";
            $response = $pdo->query($req);
            $counter = $pdo->rowCount();
            
            if($counter>0){
                $_SESSION['noMatchday'] = false;
                $val .= "        <ul>\n";
                $val .= "            <li><a href='index.php?page=matchday'>" . (Language::title('listMatchdays')) . "</a></li>\n";
                $val .= "        </ul>\n";
                // Select form
                $list = "<form action='index.php?page=matchday' method='POST'>\n";
                $list .= $form->label(Language::title('selectTheMatchday'));
                $list .= $form->selectSubmit("matchdaySelect", $response);
                $list .= "</form>\n";
                
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
                    // $form->setValues($data);
                    $val .= "<form action='index.php?page=matchday' method='POST'>\n";
                    $val .=  $form->label(Language::title('quickNav'));
                    $val .=  $form->inputHidden("matchdaySelect", $data->id_matchday . "," . $data->number);
                    $val .=  $form->submit($icon_quicknav . " " . (Language::title('MD')) . $data->number);
                    $val .= "</form>\n";
                }
                
                $val .=  $list;
                
            } else {
                $_SESSION['noMatchday'] = true;
                $val .= "          <ul>\n";
                $val .= "            <li><a href='index.php?page=matchday&create=1'>" . (Language::title('createTheMatchdays')) . "</a></li>\n";
                $val .= "          </ul>\n";
            }
            
            $val .= "        </ul>\n";
            $val .= "    </li>\n";
        }
        $val .= "    </li>\n";
        $val .= "    <li><h2>" . $icon_team . " " . (Language::title('team')) . "</h2>\n";
        $val .= "        <ul>\n";
        $val .= "            <li><a href='index.php?page=team'>" . (Language::title('marketValue')) . "</a></li>\n";
        $val .= "        </ul>\n";
        $val .= "    </li>\n";
        $val .= "    <li><h2>" . $icon_player . " " . (Language::title('player')) . "</h2>\n";
        $val .= "        <ul>\n";
        $val .= "            <li><a href='index.php?page=player'>" . (Language::title('bestPlayers')) . "</a></li>\n";
        $val .= "        </ul>\n";
        $val .= "    </li>\n";
        $val .= "</ul>\n";
        return $val;
    }
    
    static function unSet($page){
        switch($page){
            case "account":
                unset($_SESSION['userId']);
                unset($_SESSION['userLogin']);
                unset($_SESSION['language']);
                unset($_SESSION['theme']);
                unset($_SESSION['role']);
                unset($_SESSION['seasonId']);
                unset($_SESSION['seasonName']);
                unset($_SESSION['championshipId']);
                unset($_SESSION['championshipName']);
                unset($_SESSION['matchdayId']);
                unset($_SESSION['matchdayNum']);
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
        require '../theme/default/theme.php';
        $val = '';
        $val .= "  <input type='checkbox' id='fp-button' />\n";
        $val .= "  <label class='hamburger'  for='fp-button'>&#x2630;</label>\n";
        $val .= "  <div id='fp-menu'>\n";
        $val .= "  <ul>\n";
        $val .= "	 <li><a href='/'>" . (Language::title('homepage')) . "</a></li>\n";
        if(isset($_SESSION['userLogin'])){
            $val .= "	 <li><a href='index.php?page=account'>$icon_account " . (Language::title('account')) . "</a></li>\n";
            $val .= "	 <li><a href='index.php?page=season'>$icon_season " . (Language::title('season')) . "</a></li>\n";
            if(isset($_SESSION['seasonId'])){
                $val .= "	 <li><a href='index.php?page=championship'>$icon_championship " . (Language::title('championship')) . "</a></li>\n";
                if(isset($_SESSION['championshipId'])){
                    $val .= "	 <li><a href='index.php?page=matchday'>$icon_matchday " . (Language::title('matchday')) . " ".(isset($_SESSION['matchdayNum']) ? $_SESSION['matchdayNum']:NULL)."</a></li>\n";
                    $val .= "	 <li><a href='index.php?page=team'>$icon_team " . (Language::title('team')) . "</a></li>\n";
                    $val .= "	 <li><a href='index.php?page=player'>$icon_player " . (Language::title('player')) . "</a></li>\n";
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