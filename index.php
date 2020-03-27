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

// Popup if needed
    // Season selected
    if(isset($_POST['seasonSelect'])) {
        $v=explode(",",$_POST['seasonSelect']);
        $_SESSION['seasonId']=$v[0];
        $_SESSION['seasonName']=$v[1];
        echo "<section>\n";
        popup("$title_season ".$_SESSION['seasonName'],"/");
        echo "</section>\n";
    }
    // Championship selected
    if(isset($_POST['championshipSelect'])) {
        $v=explode(",",$_POST['championshipSelect']);
        $_SESSION['championshipId']=$v[0];
        $_SESSION['championshipName']=$v[1];
        echo "<section>\n";
        popup($_SESSION['championshipName'],"/");
        echo "</section>\n";
    }
    // Matchday selected
    if(isset($_POST['matchdaySelect'])&&(!isset($_SESSION['matchdayId']))) {
        $v=explode(",",$_POST['matchdaySelect']);
        $_SESSION['matchdayId']=$v[0];
        $_SESSION['matchdayNum']=$v[1];
        echo "<section>\n";
        popup("$title_matchday ".$_SESSION['matchdayNum'],"/");
        echo "</section>\n";
    }
// Check the page value
$page="";
if(isset($_GET['page'])){
    $page=$_GET['page'];
    if($page=="home") $_SESSION = array();
}
// Choose a season, a championship...
if((empty($_SESSION['seasonId']))or(empty($_SESSION['championshipId']))) include("include/inc_home.php");
// ...or display the index page
else {
    if($page!="") include($page.".php");
    else {
        include("index_nav.php"); // Navigation
        
        // Section with menu
        echo "<section>\n";
            
        echo "        <ul class='menu'>\n";
        echo "            <li><h2>";
        echo $_SESSION['championshipName'];
        echo "</h2>\n                <ul>\n";
        echo "                    <li><a href='index.php?page=dashboard'>$title_dashboard<br /><big>&#127942;</big></a></li>\n";
        echo "                    <li><a href='index.php?page=championship'>$title_standing<br /><big>&#127942;</big></a></li>\n";
        echo "               </ul>\n";
        echo "            </li>\n";
        if(isset($_SESSION['matchdayId'])){
            echo "            <li><h2>$title_MD".$_SESSION['matchdayNum']."</h2>\n";
            echo "                <ul>\n";
            echo "                    <li><a href='index.php?page=matchday'>$title_statistics<br /><big>&#128198;</big></a></li>\n";
            echo "                    <li><a href='index.php?page=teamOfTheWeek'>$title_teamOfTheWeek<br /><big>&#128198;</big></a></li>\n";
            echo "                    <li><a href='index.php?page=prediction'>$title_predictions<br /><big>&#128198;</big></a></li>\n";
            echo "                    <li><a href='index.php?page=results'>$title_results<br /><big>&#128198;</big></a></li>\n";
            echo "                </ul>\n";
            echo "            </li>\n";
        } else {
            echo "            <li><h2>$title_matchday</h2>\n";
            echo "                <ul>\n";
            echo "   <form action='index.php' method='POST'>\n";
            echo "    <label>$title_selectTheMatchday :</label>\n";        
            include("matchday_select.php");
            echo "      <noscript><input type='submit'></noscript>\n";
            echo "	 </form>\n";
            
            $response = $db->query("SELECT DISTINCT j.number FROM matchday j 
            LEFT JOIN matchgame m ON m.id_matchday=j.id_matchday 
            WHERE m.result='' AND j.id_season=".$_SESSION['seasonId']." AND j.id_championship=".$_SESSION['championshipId']." ORDER BY j.number;"); 
            $data = $response->fetch();
            echo "  	<form action='index.php' method='POST'>";
            echo "<input type='hidden' name='matchdaySelect' value='".$data['id_matchday'].",".$data['number']."'>";
            echo "<input type='submit' value='$title_MD".$data['number']."'>";
            echo "</form>\n";
            
            echo "                </ul>\n";
            echo "            </li>\n";
        }   
        echo "            <li><h2>$title_team</h2>\n";
        echo "                <ul>\n";
        echo "                    <li><a href='index.php?page=marketValue'>$title_marketValue<br /><big>&#127933;</big></a></li>\n";
        echo "                </ul>\n";
        echo "            </li>\n";
        echo "            <li><h2>$title_player</h2>\n";
        echo "                <ul>\n";
        echo "                    <li><a href='index.php?page=player'>$title_bestPlayers<br /><big>&#127939;</big></a></li>\n";
        echo "                </ul>\n";
        echo "            </li>\n";
        echo "        </ul>\n";
        echo "</section>\n";
    }
}
?>
<?php include("include/inc_footer.php");?>
