<?php 
/**
 * 
 * Class Menu
 * Manage Menu page
 */
namespace FootballPredictions\Menu;
use FootballPredictions\Language;
use FootballPredictions\Theme;

class Menu
{
    public function __construct(){
        
    }
    
    static function menu(){
        // Display menu with hamburger button
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
                                                            .Theme::icon('team') . "&nbsp;"
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