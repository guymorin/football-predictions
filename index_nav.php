<?php
// Index navigation include file
echo "<nav>\n";
echo "  <a href='index.php?page=home'>".$_SESSION['seasonName']." &#10060;</a>\n";
echo "  <a href='index.php?page=championship&exit=1'>".$_SESSION['championshipName']." &#10060;</a>\n";
if(isset($_SESSION['matchdayId'])) {
    echo "  	<a href='index.php?page=matchday&exit=1'>J".$_SESSION['matchdayNum']." &#10060;</a>\n"; // Sortir
}
echo "</nav>\n";

?>

</nav>