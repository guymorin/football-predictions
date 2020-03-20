<?php
// Connexion
try
{
	$bdd = new PDO('mysql:host=localhost;dbname=phpmyadmin;charset=utf8', 'phpmyadmin', 'master');
}
catch (Exception $e)
{
        die('Erreur : ' . $e->getMessage());
}
?>
