<?php 
use FootballPredictions\Submenu;

$submenu = new Submenu();
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
    document.getElementById("fp-submenu").className = "off";
  } else {
    document.getElementById("fp-submenu").className = "";
  }
}
</script>
<header>
    <nav id='fp-submenu'>
    <?= $submenu->championship($db);?>
    </nav>
	<?php require("fp-menu.php");?>
    <h1><a href="/"><?= "$title_site ".(isset($_SESSION['seasonName']) ? $_SESSION['seasonName'] : null);?></a></h1>
</header>
