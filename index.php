<?php session_start();?>
<?php 
/* This is the Football Predictions index page */
/* Author : Guy Morin */

// Files to include
include("lang/fr.php");
include("include/inc_header.php");
include("include/inc_connection.php");
include("include/inc_classes.php");
include("include/inc_functions.php");

// Popup if there is a posted value
    // Season selected
    if(isset($_POST['choixSaison'])) {
        $v=explode(",",$_POST['choixSaison']);
        $_SESSION['idSaison']=$v[0];
        $_SESSION['nomSaison']=$v[1];
        echo "<section>\n";
        popup("$season : ".$_SESSION['nomSaison'].".","/");
        echo "</section>\n";
    }
    // Championship selected
    if(isset($_POST['choixChampionnat'])) {
        $v=explode(",",$_POST['choixChampionnat']);
        $_SESSION['idChampionnat']=$v[0];
        $_SESSION['nomChampionnat']=$v[1];
        echo "<section>\n";
        popup("$championship : ".$_SESSION['nomChampionnat'].".","/");
        echo "</section>\n";
    }
    // Matchday selected
    if(isset($_POST['choixJournee'])&&(!isset($_SESSION['idJournee']))) {
        $v=explode(",",$_POST['choixJournee']);
        $_SESSION['idJournee']=$v[0];
        $_SESSION['numJournee']=$v[1];
        echo "<section>\n";
        popup("$matchday : J".$_SESSION['numJournee'].".","/");
        echo "</section>\n";
    }
// Check the page value
$page="";
if(isset($_GET['page'])){
    $page=$_GET['page'];
    if($page=="home") $_SESSION = array();
}
// Choose a season, a championship...
if((empty($_SESSION['idSaison']))or(empty($_SESSION['idChampionnat']))) include("home.php");
// ...or display the index page
else {
    if($page!="") include($page.".php");
    else {
        // Navigation
        echo "<nav>\n";
        echo "  <a href=\"index.php?page=home\">".$_SESSION['nomSaison']." &#10060;</a>\n";
        echo "  <a href=\"index.php?page=championship&exit=1\">".$_SESSION['nomChampionnat']." &#10060;</a>\n";
        if(isset($_SESSION['idJournee'])) {
            echo "  	<a href=\"index.php?page=matchday&exit=1\">J".$_SESSION['numJournee']." &#10060;</a>\n"; // Sortir
        }
        echo "	<a href=\"index.php?page=season\">&#127937; $season</a>\n";
        echo "	<a href=\"index.php?page=dashboard\">&#127942; $championship</a>\n";
        echo "	<a href=\"index.php?page=matchday\">&#128198; $matchday</a>\n";
        echo "	<a href=\"index.php?page=value\">&#9876; $team</a>\n";
        echo "	<a href=\"index.php?page=player\">&#127939; $player</a>\n";
        echo "</nav>\n";
        
        // Section with menu
        echo "<section>\n";
            
        echo "        <ul class=\"menu\">\n";
        echo "            <li><h2>";
        echo $_SESSION['nomChampionnat'];
        echo "</h2>\n                <ul>\n";
        echo "                    <li><a href=\"index.php?page=dashboard\">$dashboard<br /><big>&#127942;</big></a></li>\n";
        echo "                    <li><a href=\"index.php?page=championship\">$standing<br /><big>&#127942;</big></a></li>\n";
        echo "               </ul>\n";
        echo "            </li>\n";
        if(isset($_SESSION['idJournee'])){
            echo "            <li><h2>$matchday ".$_SESSION['numJournee']."</h2>\n";
            echo "                <ul>\n";
            echo "                    <li><a href=\"index.php?page=matchday\">$statistics<br /><big>&#128198;</big></a></li>\n";
            echo "                    <li><a href=\"index.php?page=teamOfTheWeek\">$teamOfTheWeek<br /><big>&#128198;</big></a></li>\n";
            echo "                    <li><a href=\"index.php?page=predictions\">$predictions<br /><big>&#128198;</big></a></li>\n";
            echo "                    <li><a href=\"index.php?page=results\">$results<br /><big>&#128198;</big></a></li>\n";
            echo "                </ul>\n";
            echo "            </li>\n";
        } else {
            echo "            <li><h2>$matchday</h2>\n";
            echo "                <ul>\n";
            echo "   <form action=\"index.php\" method=\"POST\">\n";
            echo "    <label>$selectTheMatchday :</label>\n";        
            include("matchday_select.php");
            echo "      <noscript><input type=\"submit\"></noscript>\n";
            echo "	 </form>\n";
            
            $reponse = $bdd->query("SELECT DISTINCT j.numero FROM journees j 
            LEFT JOIN matchs m ON m.id_journee=j.id_journee 
            WHERE m.resultat='' AND j.id_saison=".$_SESSION['idSaison']." AND j.id_championnat=".$_SESSION['idChampionnat']." ORDER BY j.numero;"); 
            $donnees = $reponse->fetch();
            echo "  	<form action=\"index.php\" method=\"POST\">";
            echo "<input type=\"hidden\" name=\"choixJournee\" value=\"".$donnees['num_journee'].",".$donnees['numero']."\">";
            echo "<input type=\"submit\" value=\"J".$donnees['numero']."\">";
            echo "</form>\n";
            
            echo "                </ul>\n";
            echo "            </li>\n";
        }   
        echo "            <li><h2>$team</h2>\n";
        echo "                <ul>\n";
        echo "                    <li><a href=\"index.php?page=value\">$marketValue<br /><big>&#9876;</big></a></li>\n";
        echo "                </ul>\n";
        echo "            </li>\n";
        echo "            <li><h2>$player</h2>\n";
        echo "                <ul>\n";
        echo "                    <li><a href=\"index.php?page=player\">$bestPlayers<br /><big>&#127939;</big></a></li>\n";
        echo "                </ul>\n";
        echo "            </li>\n";
        echo "        </ul>\n";
        echo "</section>\n";
    }
}
?>
<?php include("include/inc_footer.php");?>
