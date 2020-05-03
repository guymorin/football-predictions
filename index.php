<?php session_start();?>
<?php 
/* This is the Football Predictions index page */
/* Author : Guy Morin */
// Class
require 'vendor/autoload.php';

// Namespaces
use FootballPredictions\App;

use FootballPredictions\Errors;
use FootballPredictions\Forms;
use FootballPredictions\Language;
use FootballPredictions\Section\Home;
use FootballPredictions\Theme;

// Language
if(empty($_SESSION['language'])) Language::getBrowserLang();

// File to include
require 'include/functions.php';

$pdo = App::getDb();
$error = new Errors();
$form = new Forms($_POST);
$page = '';
if($pdo==false) {
    $page = 'install';
}
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
if($page!='install'){
    if(empty($_SESSION['userLogin'])) $page="account";
    elseif(empty($_SESSION['seasonId'])) $page="season";
    elseif(
        (empty($_SESSION['championshipId']))
        &&($page=="")
        ) $page="championship";
}

/* Header */

?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
    <?= Theme::style();?>
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
<?= Home::submenu($pdo, $form, $page, $create, $modify, $modifyuser);?>
    </nav>
    <nav id='fp'>
	<?= Home::menu();?>
	</nav>
	<?= App::setTitle(Language::title('site'));?>
    <h1><a href="/"><a href="/"><?= App::getTitle();?></a></h1>
</header>
<section>
<?php
if($page!='' && $page!='dump') require("pages/" . $page . ".php");
    else {
        echo "<h2>" . Language::title('homepage') . "</h2>\n";
        echo Home::homeMenu($pdo, $form);
        if($page=='dump') require 'pages/dump.php';
    }
?>
</section>
<footer>
    
</footer>

</body>

</html>

