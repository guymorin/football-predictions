<?php
/* Include to display the FP main menu */
echo "<nav id='fp'>\n";
echo "  <input type='checkbox' id='fp-button' />\n";
echo "  <label for='fp-button'>&#x2630;</label>\n";
echo "  <div id='fp-menu'>\n";
echo "  <ul>\n";
echo "	 <li><a href='/'>&#127968; $title_homepage</a></li>\n";
echo "	 <li><a href='index.php?page=season'>&#127937; $title_season</a></li>\n";
echo "	 <li><a href='index.php?page=dashboard'>&#127942; $title_championship</a></li>\n";
echo "	 <li><a href='index.php?page=matchday'>&#128198; $title_matchday</a></li>\n";
echo "	 <li><a href='index.php?page=marketValue'>&#127933; $title_team</a></li>\n";
echo "	 <li><a href='index.php?page=player'>&#127939; $title_player</a></li>\n";
echo "  </ul>\n";
echo "  </div>\n";
echo "</nav>\n";
?>