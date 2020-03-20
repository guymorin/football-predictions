<?php
// NAVIGATION JOURNEES
$reponse = $bdd->query("SELECT DISTINCT numero FROM journees 
        WHERE id_saison=".$_SESSION['idSaison']." AND id_championnat=".$_SESSION['idChampionnat']." ORDER BY numero;");
                            
echo "  	<select name=\"choixJournee\" onchange=\"submit()\">\n";
echo "  		<option value=\"0\">...</option>\n";
// On affiche chaque entrée
while ($donnees = $reponse->fetch())
{
    echo "  		<option value=\"".$donnees['id_journee'].",".$donnees['numero']."\">J".$donnees['numero']."</option>\n";
}
echo "	 </select>\n";
$reponse->closeCursor(); // Termine le traitement de la requête
?>
    
