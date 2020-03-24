<?php
// Matchday navigation include file
echo "  <nav>\n";
echo "  	<a href='/'>&#8617</a>\n"; // Back
if(isset($_SESSION['matchdayId'])) {
    echo "  	<a href='index.php?page=matchday&exit=1'>$title_MD".$_SESSION['matchdayNum']." &#10060;</a>\n";
    echo "  	<a href='index.php?page=matchday'>$title_statistics</a>\n";
    echo "  	<a href='index.php?page=prediction'>$title_predictions</a>\n";
    echo "  	<a href='index.php?page=results'>$title_results</a>\n";
    echo "  	<a href='index.php?page=teamOfTheWeek'>$title_teamOfTheWeek</a>\n";
    echo "  	<a href='index.php?page=match&create=1'>$title_createAMatch</a>\n";
    echo "  	<a href='index.php?page=match&modify=1'>$title_modifyAMatch</a>\n";
} else echo "  	<a href='index.php?page=matchday&create=1'>$title_createAMatchday</a>\n";
echo "  </nav>\n";
?>

