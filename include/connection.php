<?php
try
{
	$db = new PDO('mysql:host=localhost;dbname=phpmyadmin;charset=utf8', 'phpmyadmin', 'master');
}
catch (Exception $e)
{
        die('Erreur : ' . $e->getMessage());
}

use FootballPredictions\Database;
$pdo = new Database('localhost', 'phpmyadmin', 'phpmyadmin', 'master');

?>
