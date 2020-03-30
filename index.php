<?php session_start();?>
<?php 
/* This is the Football Predictions index page */
/* Author : Guy Morin */

// Files to include
require("lang/fr.php");
require("include/header.php");
require("include/connection.php");
require("include/functions.php");

// Class
require "class/Errors.php";
$error = new Errors();

// Popup if needed
    // Season selected
    if(isset($_POST['seasonSelect'])) {
        $v=explode(",",$_POST['seasonSelect']);
        $_SESSION['seasonId']=$error->check("Digit",$v[0]);
        $_SESSION['seasonName']=$error->check("Alnum",$v[1]);
        header('Location:index.php');
    }
    // Championship selected
    if(isset($_POST['championshipSelect'])) {
        $v=explode(",",$_POST['championshipSelect']);
        $_SESSION['championshipId']=$error->check("Digit",$v[0]);
        $_SESSION['championshipName']=$error->check("Alnum",$v[1]);
        header('Location:index.php');
    }
    // Matchday selected
    if(isset($_POST['matchdaySelect'])&&(!isset($_SESSION['matchdayId']))) {
        $v=explode(",",$_POST['matchdaySelect']);
        $_SESSION['matchdayId']=$error->check("Digit",$v[0]);
        $_SESSION['matchdayNum']=$error->check("Digit",$v[1]);
        header('Location:index.php');
    }
// Check the page value
$page="";
$create=$exit=0;
if(isset($_GET['page'])) $page=$error->check("Alnum",$_GET['page']);
if(isset($_GET['create'])) $create=$error->check("Action",$_GET['create']);
if(isset($_GET['exit'])) $exit=$error->check("Action",$_GET['exit']);

// Exit
if($exit==1){
    switch($page){
        case "season":
            unset($_SESSION['seasonId']);
            unset($_SESSION['seasonName']);
            unset($_SESSION['championshipId']);
            unset($_SESSION['championshipName']);
            unset($_SESSION['matchdayId']);
            unset($_SESSION['matchdayNum']);
            break;
        case "championship":
            unset($_SESSION['championshipId']);
            unset($_SESSION['championshipName']);
            unset($_SESSION['matchdayId']);
            unset($_SESSION['matchdayNum']);
            break;
        case "matchday":
            unset($_SESSION['matchdayId']);
            unset($_SESSION['matchdayNum']);
            break;
    }
    header('Location:index.php');
}

// Choose a season, a championship...
if(empty($_SESSION['seasonId'])) require("include/home.php");
// ...or display the index page
elseif(empty($_SESSION['championshipId'])) {
    if(($page=="championship")&&($create==1)) require($page.".php");
    else require("include/home.php");
    // ...or display the index page
} else {
    if($page!="") require($page.".php");
    else {
        require("index_nav.php"); // Navigation
        
        // Section with menu
        echo "<section>\n";
            
        echo "<ul class='menu'>\n";
        echo "    <li><h2>$icon_championship $title_championship</h2>\n";
        echo "       <ul>\n";
        echo "            <li><a href='index.php?page=dashboard'>$title_dashboard<br /><big>$icon_championship</big></a></li>\n";
        echo "            <li><a href='index.php?page=championship'>$title_standing<br /><big>$icon_championship</big></a></li>\n";
        echo "       </ul>\n";
        echo "    </li>\n";
        echo "    <li><h2>$icon_matchday $title_matchday ".(isset($_SESSION['matchdayNum']) ? $_SESSION['matchdayNum']:NULL)."</h2>\n";
        if(isset($_SESSION['matchdayId'])){
            echo "        <ul>\n";
            echo "            <li><a href='index.php?page=matchday'>$title_statistics<br /><big>$icon_matchday</big></a></li>\n";
            echo "            <li><a href='index.php?page=teamOfTheWeek'>$title_teamOfTheWeek<br /><big>$icon_matchday</big></a></li>\n";
            echo "            <li><a href='index.php?page=prediction'>$title_predictions<br /><big>$icon_matchday</big></a></li>\n";
            echo "            <li><a href='index.php?page=results'>$title_results<br /><big>$icon_matchday</big></a></li>\n";
            echo "        </ul>\n";
        } else {
            echo "        <ul>\n";

            require("matchday_select.php");
            
            echo "        </ul>\n";
            echo "    </li>\n";
        }
        echo "    </li>\n";
        echo "    <li><h2>$icon_team $title_team</h2>\n";
        echo "        <ul>\n";
        echo "            <li><a href='index.php?page=marketValue'>$title_marketValue<br /><big>$icon_team</big></a></li>\n";
        echo "        </ul>\n";
        echo "    </li>\n";
        echo "    <li><h2>$icon_player $title_player</h2>\n";
        echo "        <ul>\n";
        echo "            <li><a href='index.php?page=player'>$title_bestPlayers<br /><big>$icon_player</big></a></li>\n";
        echo "        </ul>\n";
        echo "    </li>\n";
        echo "</ul>\n";
        echo "</section>\n";
    }
}
?>
<?php require("include/footer.php");?>
