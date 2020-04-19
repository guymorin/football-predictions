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
use FootballPredictions\Language;
use FootballPredictions\Section\Championship;
use FootballPredictions\Section\Home;
use FootballPredictions\Section\Matchday;
use FootballPredictions\Section\Player;
use FootballPredictions\Section\Season;
use FootballPredictions\Section\Team;
use FootballPredictions\Section\Account;

// Language
if(empty($_SESSION['language'])) Language::getBrowserLang();

// Files to include
require '../include/connection.php';
require '../include/functions.php';

$error = new Errors();
$form = new Forms($_POST);
$page = '';
if(isset($_GET['page'])) $page=$error->check("Alnum",$_GET['page']);

// Popup if needed
    // Season selected
    if(isset($_POST['seasonSelect'])){
        $v=explode(",",$_POST['seasonSelect']);
        $_SESSION['seasonId'] = $error->check("Digit",$v[0]);
        $_SESSION['seasonName'] = $error->check("Alnum",$v[1]);
        $req = "UPDATE fp_user SET last_season = '" . $_SESSION['seasonId'] . "' 
        WHERE id_fp_user = '" . $_SESSION['userId'] . "';";
        $pdo->exec($req);
        header('Location:index.php');
    }
    // Championship selected
    if(isset($_POST['championshipSelect'])){
        $v=explode(",",$_POST['championshipSelect']);
        $_SESSION['championshipId'] = $error->check("Digit",$v[0]);
        $_SESSION['championshipName'] = $error->check("Alnum",$v[1]);
        $req = "UPDATE fp_user SET last_championship = '" . $_SESSION['championshipId'] . "'
        WHERE id_fp_user = '" . $_SESSION['userId'] . "';";
        $pdo->exec($req);
        header('Location:index.php');
    }
    // Matchday selected
    if(isset($_POST['matchdaySelect'])&&(!isset($_SESSION['matchdayId']))){
        $v=explode(",",$_POST['matchdaySelect']);
        $_SESSION['matchdayId'] = $error->check("Digit",$v[0]);
        $_SESSION['matchdayNum'] = $error->check("Digit",$v[1]);
        $url = '';
        if($page!='') $url='?page=' . $page;
        header('Location:index.php' . $url);
    }
// Check the page value
$create = $modify = $delete = $exit = $logon = $modifyuser = 0;
isset($_GET['create'])          ? $create = $error->check("Action",$_GET['create']) : null;
$create==0 && isset($_POST['create'])  ? $create = $error->check("Action",$_POST['create']) : null;
isset($_GET['modify'])          ? $modify = $error->check("Action",$_GET['modify']) : null;
isset($_POST['modify'])         ? $modify = $error->check("Action",$_POST['modify']) : null;
isset($_POST['delete'])         ? $delete = $error->check("ActionDelete",$_POST['delete']) : null;
if(isset($_GET['exit'])) $exit=$error->check("Action",$_GET['exit']);
isset($_POST['logon'])          ? $logon = $error->check("Action",$_POST['logon']) : null;
isset($_POST['modifyuser'])     ? $modifyuser = $error->check("Action",$_POST['modifyuser']) : null;

// Exit
if($exit==1){
    Home::unSet($page);
    header('Location:index.php');
}

// Choose a season, a championship...
if(empty($_SESSION['userLogin'])) $page="account";
elseif(empty($_SESSION['seasonId'])) $page="season";
elseif(
    (empty($_SESSION['championshipId']))
    &&($page=="")
) $page="championship";


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
<?= Home::submenu($pdo, $form, $page, $create, $modify);?>
    </nav>
    <nav id='fp'>
	<?= Home::menu();?>
	</nav>
	<?= App::setTitle(Language::title('site'));?>
    <h1><a href="/"><a href="/"><?= App::getTitle();?></a></h1>
</header>
<section>
<?php
    if($page!="") require("../pages/" . $page . ".php");
    else  echo Home::homeMenu($pdo, $form);
?>
</section>
<footer>
    
</footer>

</body>

</html>

