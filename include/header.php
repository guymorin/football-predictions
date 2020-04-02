<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="utf-8">
    <?php require("../theme/default/theme.php");?>
</head>

<body>

<header>
	<?php require("fp-menu.php");?>
    <h1><a href="/"><?php echo "$title_site ".(isset($_SESSION['seasonName']) ? $_SESSION['seasonName'] : null);?></a></h1>
</header>
