<?php session_start();?>
<?php 
/* This is the Football Predictions index page */
/* Author : Guy Morin */

// Class
require '../vendor/autoload.php';

// Namespaces
use FootballPredictions\App;
use FootballPredictions\Database;
use FootballPredictions\Errors;
use FootballPredictions\Forms;
use FootballPredictions\Section\Championship;
use FootballPredictions\Section\Matchday;
use FootballPredictions\Section\Player;
use FootballPredictions\Section\Season;
use FootballPredictions\Section\Team;

// Files to include
require '../lang/fr.php';
require '../include/connection.php';
require '../include/functions.php';

$error = new Errors();
$form = new Forms($_POST);

// Popup if needed
    // Season selected
    if(isset($_POST['seasonSelect'])){
        $v=explode(",",$_POST['seasonSelect']);
        $_SESSION['seasonId']=$error->check("Digit",$v[0]);
        $_SESSION['seasonName']=$error->check("Alnum",$v[1]);
        header('Location:index.php');
    }
    // Championship selected
    if(isset($_POST['championshipSelect'])){
        $v=explode(",",$_POST['championshipSelect']);
        $_SESSION['championshipId']=$error->check("Digit",$v[0]);
        $_SESSION['championshipName']=$error->check("Alnum",$v[1]);
        header('Location:index.php');
    }
    // Matchday selected
    if(isset($_POST['matchdaySelect'])&&(!isset($_SESSION['matchdayId']))){
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
if(empty($_SESSION['seasonId'])) $page="season";
elseif(
    (empty($_SESSION['championshipId']))
    &&($page=="")
)$page="championship";


/* Header */

?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
    <?php require("../theme/default/theme.php");?>

</head>

<body>
<script>
window.onscroll = function() {myFunction()};

function myFunction() {
  if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
    document.getElementById('fp-submenu').className = "off";
  } else {
    document.getElementById('fp-submenu').className = "";
  }
}
</script>
<header>
    <nav id='fp-submenu'>
<?php
switch($page){
    case "championship":
    case "dashboard":
        echo Championship::submenu($pdo, $form);
        break;
    case "matchday":
    case "match":
    case "prediction":
    case "results":
    case "teamOfTheWeek":
        echo Matchday::submenu($pdo, $form);
        break;
    case "player":
        echo Player::submenu($pdo, $form);
        break;
    case "season":
        echo Season::submenu($pdo, $form);
        break;
    case "team":
    case "marketValue":
        echo Team::submenu($pdo, $form);
        break;
    default:
        echo "  <a href='index.php?page=season&exit=1'>".$_SESSION['seasonName']." &#10060;</a>";
        echo "<a href='index.php?page=championship&exit=1'>".$_SESSION['championshipName']." &#10060;</a>";
        if(isset($_SESSION['matchdayId'])){
            echo "<a href='index.php?page=matchday&exit=1'>".$title_MD.$_SESSION['matchdayNum']." &#10060;</a>\n"; // Sortir
        }
        break;
}
?>
    </nav>
	<?php require("../include/fp-menu.php");?>
	<?= App::setTitle($title_site);?>
    <h1><a href="/"><a href="/"><?= App::getTitle();?></a></h1>
</header>

<?php

/* Page */

echo "<section>\n";
if($page!="") require("../pages/" . $page . ".php");
else {
    
    // Section with menu
        
    echo "<ul class='menu'>\n";
    echo "    <li><h2>$icon_championship $title_championship</h2>\n";
    echo "       <ul>\n";
    echo "            <li><a href='index.php?page=championship'>$title_standing</a></li>\n";
    echo "            <li><a href='index.php?page=dashboard'>$title_dashboard</a></li>\n";
    echo "       </ul>\n";
    echo "    </li>\n";
    echo "    <li><h2>$icon_matchday $title_matchday " . (isset($_SESSION['matchdayNum']) ? $_SESSION['matchdayNum']:NULL)."</h2>\n";
    if(isset($_SESSION['matchdayId'])){
        echo "        <ul>\n";
        echo "            <li><a href='index.php?page=matchday'>$title_statistics</a></li>\n";
        echo "            <li><a href='index.php?page=teamOfTheWeek'>$title_teamOfTheWeek</a></li>\n";
        echo "            <li><a href='index.php?page=prediction'>$title_predictions</a></li>\n";
        echo "            <li><a href='index.php?page=results'>$title_results</a></li>\n";
        echo "        </ul>\n";
    } else {
        echo "        <ul>\n";

        require '../pages/matchday_select.php';
        
        echo "        </ul>\n";
        echo "    </li>\n";
    }
    echo "    </li>\n";
    echo "    <li><h2>$icon_team $title_team</h2>\n";
    echo "        <ul>\n";
    echo "            <li><a href='index.php?page=marketValue'>$title_marketValue</a></li>\n";
    echo "        </ul>\n";
    echo "    </li>\n";
    echo "    <li><h2>$icon_player $title_player</h2>\n";
    echo "        <ul>\n";
    echo "            <li><a href='index.php?page=player'>$title_bestPlayers</a></li>\n";
    echo "        </ul>\n";
    echo "    </li>\n";
    echo "</ul>\n";

}
echo "</section>\n";
?>

<footer>
    
</footer>

</body>

</html>

