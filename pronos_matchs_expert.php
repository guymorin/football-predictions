<?php
    
    
// On affiche les matchs avec les critères
        
        echo "  	<form id=\"criteres\" action=\"index.php?page=pronos\" method=\"POST\">\n";             // Basculer
        echo "      <input type=\"hidden\" name=\"modifie\" value=\"2\">\n"; 
        echo "    <p><h2>Pronostics J".$_SESSION['numJournee']."</h2></p>\n";
        echo "<input id=\"edition \"type=\"hidden\" name=\"expert\" value=\"0\"><input type=\"submit\" value=\"Basculer en mode normal\">";
 
        echo "      </form>\n";
        
        // Modifier
        echo "  	<form id=\"criteres\" action=\"index.php?page=pronos\" method=\"POST\" onsubmit=\"return confirm('Attention, vous allez modifier les données !');\">\n";             
        echo "      <input type=\"hidden\" name=\"modifie\" value=\"1\">\n"; 
        echo "      <input type=\"hidden\" name=\"expert\" value=\"1\">\n";
        // On affiche chaque entrée
        $req="SELECT m.id_match,
        cr.motivation_confiance1,cr.motivation_confiance2,
        cr.serie_en_cours1,cr.serie_en_cours2,
        cr.forme_physique1,cr.forme_physique2,
        cr.meteo1,cr.meteo2,
        cr.joueurs_cles1,cr.joueurs_cles2,
        cr.valeur_marchande1,cr.valeur_marchande2,
        cr.domicile_exterieur1,cr.domicile_exterieur2,
        c1.nom as nom1,c2.nom as nom2,
        m.resultat, m.date FROM matchs m 
        LEFT JOIN clubs c1 ON m.equipe_1=c1.id_club 
        LEFT JOIN clubs c2 ON m.equipe_2=c2.id_club 
        LEFT JOIN criteres cr ON cr.id_match=m.id_match 
        WHERE m.id_journee='".$_SESSION['idJournee']."' ORDER BY m.date;";
        $reponse = $bdd->query($req);
        
        while ($donnees = $reponse->fetch())
        {
            $gagne="";
            $id=$donnees['id_match'];
            $total1=
                $donnees['motivation_confiance1']
                +$donnees['serie_en_cours1']
                +$donnees['forme_physique1']
                +$donnees['meteo1']
                +$donnees['joueurs_cles1']
                +$donnees['valeur_marchande1']
                +$donnees['domicile_exterieur1'];
            $total2=
                $donnees['motivation_confiance2']
                +$donnees['serie_en_cours2']
                +$donnees['forme_physique2']
                +$donnees['meteo2']
                +$donnees['joueurs_cles2']
                +$donnees['valeur_marchande2']
                +$donnees['domicile_exterieur2'];
            if($total1>$total2) $prono="1";
            elseif($total1==$total2) $prono="N";
            elseif($total1<$total2) $prono="2";
            if($prono==$donnees['resultat']) $gagne=" ";
                
            echo "	 <table class=\"expert\">\n";
           
            echo "  		<tr>\n";
            echo "  		  <th>Crit&egrave;res</th>\n";
            echo "            <th>".$donnees['nom1']."</th>\n";
            echo "            <th>Nul<input type=\"hidden\" name=\"id_match[]\" value=\"".$donnees['id_match']."\"></th>\n";
            echo "            <th>".$donnees['nom2']."</th>\n";
            echo "          </tr>\n";
            
            echo "  		<tr>\n";
            echo "  		  <td>Motivation / Confiance</td>";
            echo "  		  <td><input size=\"1\" type=\"number\" placeholder=\"0\" name=\"motivation_confiance1[$id]\" value=\"".$donnees['motivation_confiance1']."\"></td>\n";
            echo "  		  <td></td>\n";
            echo "  		  <td><input size=\"1\" type=\"number\" placeholder=\"0\" name=\"motivation_confiance2[$id]\" value=\"".$donnees['motivation_confiance2']."\"></td>\n";
            echo "          </tr>\n";
            
            echo "  		<tr>\n";
            echo "  		  <td>Série en cours</td>";
            echo "  		  <td><input size=\"1\" type=\"number\" placeholder=\"0\" name=\"serie_en_cours1[$id]\" value=\"".$donnees['serie_en_cours1']."\"></td>\n";
            echo "  		  <td></td>\n";
            echo "  		  <td><input size=\"1\" type=\"number\" placeholder=\"0\" name=\"serie_en_cours2[$id]\" value=\"".$donnees['serie_en_cours2']."\"></td>\n";
            
            echo "  		<tr>\n";
            echo "  		  <td>Forme physique</td>";
            echo "  		  <td><input size=\"1\" type=\"number\" placeholder=\"0\" name=\"forme_physique1[$id]\" value=\"".$donnees['forme_physique1']."\"></td>\n";
            echo "  		  <td></td>\n";
            echo "  		  <td><input size=\"1\" type=\"number\" placeholder=\"0\" name=\"forme_physique2[$id]\" value=\"".$donnees['forme_physique2']."\"></td>\n";
            echo "          </tr>\n";
            
            echo "  		<tr>\n";
            echo "  		  <td>Météo</td>";
            echo "  		  <td><input size=\"1\" type=\"number\" placeholder=\"0\" name=\"meteo1[$id]\" value=\"".$donnees['meteo1']."\"></td>\n";
            echo "  		  <td></td>\n";
            echo "  		  <td><input size=\"1\" type=\"number\" placeholder=\"0\" name=\"meteo2[$id]\" value=\"".$donnees['meteo2']."\"></td>\n";
            
            echo "  		<tr>\n";
            echo "  		  <td>Joueurs clés</td>";
            echo "  		  <td><input size=\"1\" type=\"number\" placeholder=\"0\" name=\"joueurs_cles1[$id]\" value=\"".$donnees['joueurs_cles1']."\"></td>\n";
            echo "  		  <td></td>\n";
            echo "  		  <td><input size=\"1\" type=\"number\" placeholder=\"0\" name=\"joueurs_cles2[$id]\" value=\"".$donnees['joueurs_cles2']."\"></td>\n";
            echo "          </tr>\n";
            
            echo "  		<tr>\n";
            echo "  		  <td>Valeur marchande</td>";
            echo "  		  <td><input size=\"1\" type=\"number\" placeholder=\"0\" name=\"valeur_marchande1[$id]\" value=\"".$donnees['valeur_marchande1']."\"></td>\n";
            echo "  		  <td></td>\n";
            echo "  		  <td><input size=\"1\" type=\"number\" placeholder=\"0\" name=\"valeur_marchande2[$id]\" value=\"".$donnees['valeur_marchande2']."\"></td>\n";
            echo "          </tr>\n";
            
            echo "  		<tr>\n";
            echo "  		  <td>Domicile / Extérieur</td>";
            echo "  		  <td><input size=\"1\" type=\"number\" placeholder=\"0\" name=\"domicile_exterieur1[$id]\" value=\"".$donnees['domicile_exterieur1']."\"></td>\n";
            echo "  		  <td></td>\n";
            echo "  		  <td><input size=\"1\" type=\"number\" placeholder=\"0\" name=\"domicile_exterieur2[$id]\" value=\"".$donnees['domicile_exterieur2']."\"></td>\n";
            echo "          </tr>\n";

            echo "  		<tr>\n";
            echo "  		  <td>Total</td>";
            echo "  		  <td>$total1</td>\n";
            echo "  		  <td></td>\n";
            echo "  		  <td>$total2</td>\n";
            echo "          </tr>\n";

            echo "  		<tr>\n";
            echo "  		  <td>Pronostic".$gagne."</td>";
            echo "  		  <td><input type=\"radio\" readonly id=\"1\" value=\"1\"";
            if($prono=="1") echo " checked";
            echo "></td>\n";
            echo "  		  <td><input type=\"radio\" readonly id=\"N\" value=\"N\"";
            if($prono=="N") echo " checked";
            echo "></td>\n";
            echo "  		  <td><input type=\"radio\" readonly id=\"2\" value=\"2\"";
            if($prono=="2") echo " checked";
            echo "></td>\n";
            echo "          </tr>\n";
            
            
            echo "  		<tr>\n";
            echo "  		  <td>Résultat</td>";
            echo "  		  <td><input name=\"resultat[$id]\" readonly type=\"radio\" id=\"1\" value=\"1\"";
            if($donnees['resultat']=="1") echo " checked";
            echo "></td>\n";
            echo "  		  <td><input name=\"resultat[$id]\" readonly type=\"radio\" id=\"N\" value=\"N\"";
            if($donnees['resultat']=="N") echo " checked";
            echo "></td>\n";
            echo "  		  <td><input name=\"resultat[$id]\" readonly type=\"radio\" id=\"2\" value=\"2\"";
            if($donnees['resultat']=="2") echo " checked";
            echo "></td>\n";
            echo "          </tr>\n";
            
            echo "	 </table>\n";
        }
        $reponse->closeCursor(); // Termine le traitement de la requête

        echo "      <div><input type=\"submit\"></div>\n";
        echo "	 </form>\n";

?>  
