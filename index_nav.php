<?php
// Index navigation include file
echo "<nav>\n";
echo "  <a href='index.php?page=season&exit=1'>".$_SESSION['seasonName']." &#10060;</a>";
echo "<a href='index.php?page=championship&exit=1'>".$_SESSION['championshipName']." &#10060;</a>";
if(isset($_SESSION['matchdayId'])) {
    echo "<a href='index.php?page=matchday&exit=1'>".$title_MD.$_SESSION['matchdayNum']." &#10060;</a>\n"; // Sortir
}
echo "</nav>\n";

?>

</nav>