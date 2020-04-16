<?php
/* Include to display the FP main menu */
use FootballPredictions\Language;

echo "<nav id='fp'>\n";
echo "  <input type='checkbox' id='fp-button' />\n";
echo "  <label class='hamburger'  for='fp-button'>&#x2630;</label>\n";
echo "  <div id='fp-menu'>\n";
echo "  <ul>\n";
echo "	 <li><a href='/'>" . (Language::title('homepage')) . "</a></li>\n";
if(isset($_SESSION['userLogin'])){
    echo "	 <li><a href='index.php?page=account'>$icon_account " . (Language::title('account')) . "</a></li>\n";
    echo "	 <li><a href='index.php?page=season'>$icon_season " . (Language::title('season')) . "</a></li>\n";
    if(isset($_SESSION['seasonId'])){
        echo "	 <li><a href='index.php?page=championship'>$icon_championship " . (Language::title('championship')) . "</a></li>\n";
        if(isset($_SESSION['championshipId'])){
            echo "	 <li><a href='index.php?page=matchday'>$icon_matchday " . (Language::title('matchday')) . " ".(isset($_SESSION['matchdayNum']) ? $_SESSION['matchdayNum']:NULL)."</a></li>\n";
            echo "	 <li><a href='index.php?page=team'>$icon_team " . (Language::title('team')) . "</a></li>\n";
            echo "	 <li><a href='index.php?page=player'>$icon_player " . (Language::title('player')) . "</a></li>\n";
        }
    }
}
echo "  </ul>\n";
echo "  <label class='layer' for='fp-button'></label>\n";
echo "  </div>\n";
echo "</nav>\n";
?>