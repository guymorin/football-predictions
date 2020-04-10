<?php
/* Include to display the FP main menu */
echo "<nav id='fp'>\n";
echo "  <input type='checkbox' id='fp-button' />\n";
echo "  <label class='hamburger'  for='fp-button'>&#x2630;</label>\n";
echo "  <div id='fp-menu'>\n";
echo "  <ul>\n";
echo "	 <li><a href='/'>$title_homepage</a></li>\n";

echo "	 <li><a href='index.php?page=season'>$icon_season $title_season</a></li>\n";
if(isset($_SESSION['seasonId'])){
    echo "	 <li><a href='index.php?page=championship'>$icon_championship $title_championship</a></li>\n";
    if(isset($_SESSION['championshipId'])){
        echo "	 <li><a href='index.php?page=matchday'>$icon_matchday $title_matchday ".(isset($_SESSION['matchdayNum']) ? $_SESSION['matchdayNum']:NULL)."</a></li>\n";
        echo "	 <li><a href='index.php?page=marketValue'>$icon_team $title_team</a></li>\n";
        echo "	 <li><a href='index.php?page=player'>$icon_player $title_player</a></li>\n";
    }
}
echo "  </ul>\n";
echo "  <label class='layer' for='fp-button'></label>\n";
echo "  </div>\n";
echo "</nav>\n";
?>