<?php
// Player navigation include file
echo "  <nav>\n";
$response = $db->query("SELECT * FROM player ORDER BY name, firstname");
echo "  	<a href='/'>&#127968; $title_homepage</a>\n";
echo "  	<a href='index.php?page=player'>$title_bestPlayers</a>\n";
echo "  	<a href='index.php?page=player&create=1'>$title_createAPlayer</a>\n";
echo "  	<a href='index.php?page=player&modify=1'>$title_modifyAPlayer</a>\n";
echo "  </nav>\n";
$response->closeCursor();

?>