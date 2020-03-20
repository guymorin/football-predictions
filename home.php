<?php 

    if(empty($_SESSION['idSaison'])){
        $reponse = $bdd->query("SELECT * FROM saisons ORDER BY nom;"); 
        echo "  <section>\n";
        echo "  	<form action=\"index.php\" method=\"POST\">\n";             // Choisir
        echo "      <h1><big>&#127937;</big></h1><label>$selectTheSeason :</label>\n";                                    
        echo "  	<select name=\"choixSaison\" onchange=\"submit()\">\n";
        echo "  		<option value=\"0\">...</option>\n";
        // On affiche chaque entrée
        while ($donnees = $reponse->fetch())
        {
            echo "  		<option value=\"".$donnees['id_saison'].",".$donnees['nom']."\">".$donnees['nom']."</option>\n";
        }
        echo "	     </select>\n";
        echo "       <noscript><input type=\"submit\"></noscript>\n";
        echo "	     </form>\n";
        
        $reponse = $bdd->query("SELECT * FROM saisons ORDER BY id_saison DESC;"); 
        $donnees = $reponse->fetch();
        echo "  	<form action=\"index.php\" method=\"POST\">";
        echo "<input type=\"hidden\" name=\"choixSaison\" value=\"".$donnees['id_saison'].",".$donnees['nom']."\">";
        echo "<input type=\"submit\" value=\"".$donnees['nom']."\">";
        echo "</form>\n";
        
        echo "  </section>\n";
        $reponse->closeCursor(); // Termine le traitement de la requête  
    }
    if($_SESSION['idSaison']>0){
        $reponse = $bdd->query("SELECT DISTINCT c.nom FROM championnats c 
        LEFT JOIN saison_championnat_club scc ON c.id_championnat=scc.id_championnat WHERE scc.id_saison=".$_SESSION['idSaison']." ORDER BY c.nom;"); 
        echo "  <section>\n";
        echo "  	<form action=\"index.php\" method=\"POST\">\n";             // Choisir
        echo "    <h1><big>&#127942;</big></h1><label>$selectTheChampionship :</label>\n";                                    
        echo "  	<select name=\"choixChampionnat\" onchange=\"submit()\">\n";
        echo "  		<option value=\"0\">...</option>\n";
        // On affiche chaque entrée
        while ($donnees = $reponse->fetch())
        {
            echo "  		<option value=\"".$donnees['id_championnat'].",".$donnees['nom']."\">".$donnees['nom']."</option>\n";
        }
        echo "	 </select>\n";
        echo "      <noscript><input type=\"submit\"></noscript>\n";
        echo "	 </form>\n";
        
        $reponse = $bdd->query("SELECT * FROM championnats ORDER BY id_championnat DESC;"); 
        $donnees = $reponse->fetch();
        echo "  	<form action=\"index.php\" method=\"POST\">";
        echo "<input type=\"hidden\" name=\"choixChampionnat\" value=\"".$donnees['id_championnat'].",".$donnees['nom']."\">";
        echo "<input type=\"submit\" value=\"".$donnees['nom']."\">";
        echo "</form>\n";
        
        echo "  </section>\n";
        $reponse->closeCursor(); // Termine le traitement de la requête  
    }
?>
