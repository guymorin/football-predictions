<?php
// NAVIGATION CHAMPIONNAT
echo "  <nav>\n";
$reponse = $bdd->query("SELECT DISTINCT c.nom FROM championnats c 
        LEFT JOIN saison_championnat_club scc ON c.id_championnat=scc.id_championnat WHERE scc.id_saison=".$_SESSION['idSaison']." ORDER BY c.nom;");
echo "  	<a href=\"/\">&#8617</a>\n";                                   // Retour
echo "  	<a href=\"index.php?page=championnats&sortie=1\">".$_SESSION['nomChampionnat']." &#10060;</a>\n"; // Sortir
echo "  	<a href=\"index.php?page=tableau\">Tableau de bord</a>\n";
echo "  	<a href=\"index.php?page=championnats\">Classement</a>\n";
echo "  	<a href=\"index.php?page=championnats&cree=1\">Créer un championnat</a>\n"; // Créer

echo "  	<form action=\"index.php?page=championnats\" method=\"POST\">\n";             // Modifier
echo "      <input type=\"hidden\" name=\"modifie\" value=\"1\">\n"; 
echo "    <label>Modifier un championnat :</label>\n";                                    
echo "  	<select name=\"id_championnat\" onchange=\"submit()\">\n";
echo "  		<option value=\"0\">...</option>\n";
// On affiche chaque entrée
while ($donnees = $reponse->fetch())
{
    echo "  		<option value=\"".$donnees['id_championnat']."\"";
    if($donnees['id_championnat']==$_SESSION['idChampionnat']) echo " disabled";
    echo ">".$donnees['nom']."</option>\n";
}
echo "	 </select>\n";
echo "      <noscript><input type=\"submit\"></noscript>\n";
echo "	 </form>\n";

echo "      </nav>\n";
$reponse->closeCursor(); // Termine le traitement de la requête

?>
