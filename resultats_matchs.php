<?php
    
changeJ($bdd,"Résultats","resultats");
    
// On affiche les matchs
        
        echo "  	<form id=\"resultats\" action=\"index.php?page=resultats\" method=\"POST\" onsubmit=\"return confirm('Attention, vous allez modifier les données !');\">\n";             // Modifier
        echo "      <input type=\"hidden\" name=\"modifie\" value=\"1\">\n"; 
        //echo "    <p>Résultats J".$_SESSION['numJournee']."</p>\n";                                    
        // On affiche chaque entrée
        $req="SELECT m.id_match,
        c1.nom as nom1,c2.nom as nom2,
        m.resultat, m.date, m.cote1, m.coteN, m.cote2, m.rouge1, m.rouge2, m.blesse1, m.blesse2 FROM matchs m 
        LEFT JOIN clubs c1 ON m.equipe_1=c1.id_club 
        LEFT JOIN clubs c2 ON m.equipe_2=c2.id_club 
        WHERE m.id_journee='".$_SESSION['idJournee']."' ORDER BY m.date;";
        $reponse = $bdd->query($req);

        echo "	 <table>\n";
           
        echo "  		<tr>\n";
        echo "            <th>Date</th>\n";
        echo "            <th>Non joué</th>\n";
        echo "  		  <th>Match</th>\n";
        echo "  		  <th>Cotes</th>\n";
        echo "            <th>1</th>\n";
        echo "            <th>Nul</th>\n";
        echo "            <th>2</th>\n";
        echo "            <th colspan=\"2\">Cartons rouges</th>\n";
        echo "            <th colspan=\"2\">Blessures</th>\n";
        echo "          </tr>\n";
            
        while ($donnees = $reponse->fetch())
        {
            $id=$donnees['id_match'];
            
            echo "<input type=\"hidden\" name=\"id_match[]\" value=\"".$donnees['id_match']."\">\n";
            
            echo "  		<tr>\n";
            echo "  		  <td><input type=\"date\" name=\"date[$id]\" value=\"".$donnees['date']."\"></td>";
            echo "  		  <td><input type=\"radio\" name=\"resultat[$id]\" value=\"\"";
            if($donnees['resultat']=="") echo " checked";
            echo "></td>\n";

            echo "  		  <td>".$donnees['nom1']." - ".$donnees['nom2']."</td>";
            
            echo "<td>\n";
            echo "  1<input type=\"number\" step=\"0.01\" name=\"cote1[$id]\" size=\"2\" value=\"".$donnees['cote1']."\">\n";
            echo "  N<input type=\"number\" step=\"0.01\" name=\"coteN[$id]\" size=\"2\" value=\"".$donnees['coteN']."\">\n";
            echo "  2<input type=\"number\" step=\"0.01\" name=\"cote2[$id]\" size=\"2\" value=\"".$donnees['cote2']."\">\n";
            echo "</td>\n";

            echo "  		  <td><input type=\"radio\" id=\"1\" name=\"resultat[$id]\" value=\"1\"";
            if($donnees['resultat']=="1") echo " checked";
            echo "></td>\n";
            echo "  		  <td><input type=\"radio\" id=\"N\" name=\"resultat[$id]\" value=\"N\"";
            if($donnees['resultat']=="N") echo " checked";
            echo "></td>\n";
            echo "  		  <td><input type=\"radio\" id=\"2\" name=\"resultat[$id]\" value=\"2\"";
            if($donnees['resultat']=="2") echo " checked";
            echo "></td>\n";
            
            echo "<td><input type=\"number\" name=\"rouge1[$id]\" value=\"".$donnees['rouge1']."\"></td>\n";
            echo "<td><input type=\"number\" name=\"rouge2[$id]\" value=\"".$donnees['rouge2']."\"></td>\n";
            
            echo "<td><input type=\"number\" name=\"blesse1[$id]\" value=\"".$donnees['blesse1']."\"></td>\n";
            echo "<td><input type=\"number\" name=\"blesse2[$id]\" value=\"".$donnees['blesse2']."\"></td>\n";

            echo "          </tr>\n";

        }
        $reponse->closeCursor(); // Termine le traitement de la requête
        echo "	 </table>\n";
            
        echo "      <div><input type=\"submit\"></div>\n";
        echo "	 </form>\n";

?>  
