<?php
// NAVIGATION JOURNEES
echo "  <nav>\n";
echo "  	<a href=\"/\">&#8617</a>\n";                                   // Retour

if(isset($_SESSION['idJournee'])) {

    echo "  	<a href=\"index.php?page=journees&sortie=1\">J".$_SESSION['numJournee']." &#10060;</a>\n"; // Sortir
    echo "  	<a href=\"index.php?page=journees\">Statistiques</a>\n"; // Stats
    echo "  	<a href=\"index.php?page=pronos\">Pronostics</a>\n"; // Critères
    echo "  	<a href=\"index.php?page=resultats\">Résultats</a>\n"; // Résultats
    echo "  	<a href=\"index.php?page=equipe_type\">&Eacute;quipe type</a>\n"; // Equipe type
    echo "  	<a href=\"index.php?page=matchs&cree=1\">Créer un match</a>\n"; // Matchs
    echo "  	<a href=\"index.php?page=matchs&modifie=1\">Modifier un match</a>\n"; // Matchs

} else echo "  	<a href=\"index.php?page=journees&cree=1\">Créer une journée</a>\n"; // Créer


echo "  </nav>\n";
?>

