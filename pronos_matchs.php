<?php
    changeJ($bdd,"Pronostics","pronos");
// On affiche les matchs avec les critères
        
        echo "  	<form id=\"criteres\" action=\"index.php?page=pronos\" method=\"POST\">\n";             
        
        // Basculer
        echo "<input type=\"hidden\" name=\"expert\" value=\"1\"><input type=\"submit\" value=\"Basculer en mode expert\">";

        echo "      </form>\n";
        
        // Modifier
        echo "  	<form id=\"criteres\" action=\"index.php?page=pronos\" method=\"POST\" onsubmit=\"return confirm('Attention, vous allez modifier les données !');\">\n";
        echo "      <input type=\"hidden\" name=\"modifie\" value=\"1\">\n"; 
        // On affiche chaque entrée
        $req="SELECT m.id_match,
        cr.motivation_confiance1,cr.motivation_confiance2,
        cr.serie_en_cours1,cr.serie_en_cours2,
        cr.forme_physique1,cr.forme_physique2,
        cr.meteo1,cr.meteo2,
        cr.joueurs_cles1,cr.joueurs_cles2,
        cr.valeur_marchande1,cr.valeur_marchande2,
        cr.domicile_exterieur1,cr.domicile_exterieur2,
        c1.nom as nom1,c2.nom as nom2,c1.id_club as eq1,c2.id_club as eq2,
        c1.code_insee,
        m.resultat, m.date FROM matchs m 
        LEFT JOIN clubs c1 ON m.equipe_1=c1.id_club 
        LEFT JOIN clubs c2 ON m.equipe_2=c2.id_club 
        LEFT JOIN criteres cr ON cr.id_match=m.id_match 
        WHERE m.id_journee='".$_SESSION['idJournee']."' ORDER BY m.date;";
        $reponse = $bdd->query($req);

            
            // Calcul des performances domiciles (meilleurs clubs)
            $req="
            SELECT c.id_club, c.nom, COUNT(m.id_match) as matchs,
            SUM(
                CASE WHEN m.resultat = '1' AND m.equipe_1=c.id_club THEN 3 ELSE 0 END +
                CASE WHEN m.resultat = 'N' AND m.equipe_1=c.id_club THEN 1 ELSE 0 END 
            ) as points
            FROM clubs c 
            LEFT JOIN saison_championnat_club scc ON c.id_club=scc.id_club 
            LEFT JOIN journees j ON (scc.id_saison=j.id_saison AND scc.id_championnat=j.id_championnat) 
            LEFT JOIN matchs m ON m.id_journee=j.id_journee 
            WHERE scc.id_saison='".$_SESSION['idSaison']."' 
            AND scc.id_championnat='".$_SESSION['idChampionnat']."' 
            AND (c.id_club=m.equipe_1 OR c.id_club=m.equipe_2) 
            AND m.resultat<>'' 
            GROUP BY c.id_club,c.nom 
            ORDER BY points DESC
            LIMIT 0,5";
            $r = $bdd->query($req);
            while($data=$r->fetchColumn(0))   $domBonus[] = $data;
            
            // Calcul des performances domiciles (pires clubs)
            $req="
            SELECT c.id_club, c.nom, COUNT(m.id_match) as matchs,
            SUM(
                CASE WHEN m.resultat = '1' AND m.equipe_1=c.id_club THEN 3 ELSE 0 END +
                CASE WHEN m.resultat = 'N' AND m.equipe_1=c.id_club THEN 1 ELSE 0 END 
            ) as points
            FROM clubs c 
            LEFT JOIN saison_championnat_club scc ON c.id_club=scc.id_club 
            LEFT JOIN journees j ON (scc.id_saison=j.id_saison AND scc.id_championnat=j.id_championnat) 
            LEFT JOIN matchs m ON m.id_journee=j.id_journee 
            WHERE scc.id_saison='".$_SESSION['idSaison']."' 
            AND scc.id_championnat='".$_SESSION['idChampionnat']."' 
            AND (c.id_club=m.equipe_1 OR c.id_club=m.equipe_2) 
            AND m.resultat<>'' 
            GROUP BY c.id_club,c.nom 
            ORDER BY points ASC
            LIMIT 0,5";
            $r = $bdd->query($req);
            while($data=$r->fetchColumn(0))   $domMalus[] = $data;
            
            // Calcul des performances extérieures (meilleurs clubs)
            $req="
            SELECT c.id_club, c.nom, COUNT(m.id_match) as matchs,
            SUM(
                CASE WHEN m.resultat = '1' AND m.equipe_2=c.id_club THEN 3 ELSE 0 END +
                CASE WHEN m.resultat = 'N' AND m.equipe_2=c.id_club THEN 1 ELSE 0 END 
            ) as points
            FROM clubs c 
            LEFT JOIN saison_championnat_club scc ON c.id_club=scc.id_club 
            LEFT JOIN journees j ON (scc.id_saison=j.id_saison AND scc.id_championnat=j.id_championnat) 
            LEFT JOIN matchs m ON m.id_journee=j.id_journee 
            WHERE scc.id_saison='".$_SESSION['idSaison']."' 
            AND scc.id_championnat='".$_SESSION['idChampionnat']."' 
            AND (c.id_club=m.equipe_1 OR c.id_club=m.equipe_2) 
            AND m.resultat<>'' 
            GROUP BY c.id_club,c.nom 
            ORDER BY points ASC
            LIMIT 0,5";
            $r = $bdd->query($req);
            while($data=$r->fetchColumn(0))   $extBonus[] = $data;
            
            // Calcul des performances extérieures (pires clubs)
            $req="
            SELECT c.id_club, c.nom, COUNT(m.id_match) as matchs,
            SUM(
                CASE WHEN m.resultat = '1' AND m.equipe_2=c.id_club THEN 3 ELSE 0 END +
                CASE WHEN m.resultat = 'N' AND m.equipe_2=c.id_club THEN 1 ELSE 0 END 
            ) as points
            FROM clubs c 
            LEFT JOIN saison_championnat_club scc ON c.id_club=scc.id_club 
            LEFT JOIN journees j ON (scc.id_saison=j.id_saison AND scc.id_championnat=j.id_championnat) 
            LEFT JOIN matchs m ON m.id_journee=j.id_journee 
            WHERE scc.id_saison='".$_SESSION['idSaison']."' 
            AND scc.id_championnat='".$_SESSION['idChampionnat']."' 
            AND (c.id_club=m.equipe_1 OR c.id_club=m.equipe_2) 
            AND m.resultat<>'' 
            GROUP BY c.id_club,c.nom 
            ORDER BY points DESC
            LIMIT 0,5";
            $r = $bdd->query($req);
            while($data=$r->fetchColumn(0))   $extMalus[] = $data;
        
        $txtProno= "	<table>\n";
        $txtProno.= "        <tr>\n";
        $txtProno.= "            <th><strong>Journée ".$_SESSION['idJournee']."</strong></th>\n";
        $txtProno.= "       </tr>\n";
            
        while ($donnees = $reponse->fetch())
        {
            // Calcul de motivation
            $motivC1=criteres("motivC1",$donnees,$bdd);
            $motivC2=criteres("motivC2",$donnees,$bdd);
            
            // Calcul de série en cours
            $serieC1=criteres("serieC1",$donnees,$bdd);
            $serieC2=criteres("serieC2",$donnees,$bdd);

            // Calcul de la VM
            $v1=criteres("v1",$donnees,$bdd);
            $v2=criteres("v2",$donnees,$bdd);
            $vm1 = round(sqrt($v1/$v2));
            $vm2 = round(sqrt($v2/$v1));
            
            // Calcul Domicile / Extérieur
            $dom = 0;
            if(in_array($donnees['eq1'],$domBonus)) $dom=1;
            if(in_array($donnees['eq1'],$domMalus)) $dom=(-1); 
            $ext = 0;
            if(in_array($donnees['eq2'],$extBonus)) $ext=1;
            if(in_array($donnees['eq2'],$extMalus)) $ext=(-1); 
            
            // Calcul de la météo
            if($donnees['date']!=""){
            
                $date1 = new DateTime($donnees['date']);
                $date2 = new DateTime(date("Y-m-d"));
                
                $diff = $date2->diff($date1)->format("%a");
                $nuage="";
                
                if($diff>=0){
                    $api="https://api.meteo-concept.com/api/forecast/daily/".$diff."?token=1aca29e38eb644104b41975b55a6842fc4fb2bfd2f79f85682baecb1c5291a3e&insee=".$donnees['code_insee'];
                    $data = file_get_contents($api);
                    $pluie=0;

                    if ($data !== false) {
                        $decoded = json_decode($data);
                        $city = $decoded->city;
                        $forecast = $decoded->forecast;
                        //echo $city->name." J+".$diff." : pluie entre ".$forecast->rr10."mm et ".$forecast->rr1."mm.";
                        $pluie=$forecast->rr1;
                    }
                    switch($pluie){
                        case ($pluie==0):
                            $nuage="&#x1F323;";// Soleil
                        case ($pluie>=0&&$pluie<1):
                            $nuage="&#x1F324;";// Pluie faible
                            $meteo=1;
                            if(round($v2/10)>round($v1/10)) $eq2Meteo=$meteo;
                            elseif(round($v2/10)==round($v1/10)) {
                                $eq1Meteo=$meteo;
                                $eq2Meteo=$meteo;
                            }
                            else {
                                $eq1Meteo=$meteo;
                                $eq2Meteo=0;
                            }
                            break;
                        case ($pluie>=1&&$pluie<3):
                            $nuage="&#x1F326;";// Pluie modérée
                            $meteo=1;
                            if(round($v2/10)>round($v1/10)) $eq1Meteo=$meteo;
                            elseif(round($v2/10)==round($v1/10)) {
                                $eq1Meteo=$meteo;
                                $eq2Meteo=$meteo;
                            }
                            else {
                                $eq1Meteo=0;
                                $eq2Meteo=$meteo;
                            }
                            break;
                        case ($pluie>=3):
                            $nuage="&#x1F327;";//Pluie forte
                            $meteo=2;
                            if(round($v2/10)>round($v1/10)) $eq1Meteo=$meteo;
                            elseif(round($v2/10)==round($v1/10)) {
                                $eq1Meteo=$meteo;
                                $eq2Meteo=$meteo;
                            }
                            else {
                                $eq1Meteo=0;
                                $eq2Meteo=$meteo;
                            }
                            break;
                    }

                }
            }
            if(($donnees['resultat']!="")||(isset($donnees['meteo1']))) $eq1Meteo=$donnees['meteo1'];
            if(($donnees['resultat']!="")||(isset($donnees['meteo2']))) $eq2Meteo=$donnees['meteo2'];
            
            // Calcul des matchs similaires
                $req="SELECT SUM(CASE WHEN m.resultat = '1' THEN 1 ELSE 0 END) AS Dom,
                SUM(CASE WHEN m.resultat = 'N' THEN 1 ELSE 0 END) AS Nul,
                SUM(CASE WHEN m.resultat = '2' THEN 1 ELSE 0 END) AS Ext
                FROM matchs m 
                LEFT JOIN criteres cr ON cr.id_match=m.id_match 
                WHERE cr.motivation_confiance1='".$donnees['motivation_confiance1']."' 
                AND cr.motivation_confiance2='".$donnees['motivation_confiance2']."' 
                AND cr.serie_en_cours1='".$donnees['serie_en_cours1']."' 
                AND cr.serie_en_cours2='".$donnees['serie_en_cours2']."' 
                AND cr.forme_physique1='".$donnees['forme_physique1']."' 
                AND cr.forme_physique2='".$donnees['forme_physique2']."' 
                AND cr.meteo1='".$eq1Meteo."' 
                AND cr.meteo2='".$eq2Meteo."' 
                AND cr.joueurs_cles1='".$donnees['joueurs_cles1']."' 
                AND cr.joueurs_cles2='".$donnees['joueurs_cles2']."' 
                AND cr.valeur_marchande1='".$donnees['valeur_marchande1']."' 
                AND cr.valeur_marchande2='".$donnees['valeur_marchande2']."' 
                AND cr.domicile_exterieur1='".$donnees['domicile_exterieur1']."' 
                AND cr.domicile_exterieur2='".$donnees['domicile_exterieur2']."' 
                AND m.date<'".$donnees['date']."'";
                $r = $bdd->query($req)->fetch();
                $similairesDom=criteres("msDom",$r,$bdd);
                $similairesNul=criteres("msNul",$r,$bdd);
                $similairesExt=criteres("msExt",$r,$bdd);
            
            // Calcul du total des critères
            $gagne="";
            $id=$donnees['id_match'];
            $total1=
                $donnees['motivation_confiance1']
                +$serieC1
                +$donnees['forme_physique1']
                +$eq1Meteo
                +$donnees['joueurs_cles1']
                +$vm1
                +$dom
                +$similairesDom;
            $total2=
                $donnees['motivation_confiance2']
                +$serieC2
                +$donnees['forme_physique2']
                +$eq2Meteo
                +$donnees['joueurs_cles2']
                +$vm2
                +$ext
                +$similairesExt;
            if($total1>$total2) $prono="1";
            elseif($total1==$total2) $prono="N";
            elseif($total1<$total2) $prono="2";
            if(($similairesNul>$total1)&&($similairesNul>$total2)) $prono="N";
            
            // tableau
            
            if($donnees['resultat']=="") echo "<input type=\"hidden\" name=\"id_match[]\" value=\"".$donnees['id_match']."\">";
            echo $similaires[0];
            echo "	 <table>\n";
           
            echo "  		<tr>\n";
            echo "  		  <th>".$donnees['date']."\n";
            echo "            <th>".$donnees['nom1']."</th>\n";
            echo "            <th>Nul</th>\n";
            echo "            <th>".$donnees['nom2']."</th>\n";
            echo "          </tr>\n";
            

            $txtProno.= "        <tr>\n";
            $txtProno.= "            <td><strong>".$donnees['nom1']." - ".$donnees['nom2']."</strong></td>\n";
            $txtProno.= "       </tr>\n";
            $txtProno.= "        <tr>\n";
            $txtProno.= "            <td>\n";
            switch($prono){
                case "1":
                    $txtProno.= "Pour ce match, je prédis une victoire de ".$donnees['nom1'];
                    break;
                case "N":
                    $txtProno.= "Pour ce match, je prédis un résultat nul";
                    break;
                case "2":
                    $txtProno.= "Pour ce match, je prédis une victoire de ".$donnees['nom2'];
                    break;
            }
            $txtProno.= "            </td>\n";
            $txtProno.= "       </tr>\n";
         
            
            echo "  		<tr>\n";
            echo "  		  <td>Motivation / Confiance</td>\n";
            if($donnees['resultat']!="") echo "<td>".$motivC1."</td>\n";
            else echo "  		  <td><input size=\"1\" type=\"number\" name=\"motivation_confiance1[$id]\" value=\"".$motivC1."\" placeholder=\"0\"></td>\n";
            echo "  		  <td></td>\n";
            if($donnees['resultat']!="") echo "<td>".$motivC2."</td>\n";
            else echo "  		  <td><input size=\"1\" type=\"number\" name=\"motivation_confiance2[$id]\" value=\"".$motivC2."\" placeholder=\"0\"></td>\n";
            echo "          </tr>\n";
            
            echo "  		<tr>\n";
            echo "  		  <td>Série en cours</td>";
            if($donnees['resultat']!="") echo "<td>".$serieC1."</td>\n";
            else echo "  		  <td><input size=\"1\" type=\"number\" name=\"serie_en_cours1[$id]\" value=\"".$serieC1."\" placeholder=\"0\"></td>\n";
            echo "  		  <td></td>\n";
            if($donnees['resultat']!="") echo "<td>".$serieC2."</td>\n";
            else echo "  		  <td><input size=\"1\" type=\"number\" name=\"serie_en_cours2[$id]\" value=\"".$serieC2."\" placeholder=\"0\"></td>\n";
            
            echo "  		<tr>\n";
            echo "  		  <td>Forme physique</td>\n";
            if($donnees['resultat']!="") echo "<td>".$donnees['forme_physique1']."</td>\n";
            else echo "  		  <td><input size=\"1\" type=\"number\" name=\"forme_physique1[$id]\" value=\"".$donnees['forme_physique1']."\" placeholder=\"0\"></td>\n";
            echo "  		  <td></td>\n";
            if($donnees['resultat']!="") echo "<td>".$donnees['forme_physique2']."</td>\n";
            else echo "  		  <td><input size=\"1\" type=\"number\" name=\"forme_physique2[$id]\" value=\"".$donnees['forme_physique2']."\" placeholder=\"0\"></td>\n";
            echo "          </tr>\n";
            
            
            echo "  		<tr>\n";
            echo "  		  <td>Météo <big>".$nuage."</big></td>\n";
            if($donnees['resultat']!="") echo "<td>".$eq1Meteo."</td>\n";
            else {
                echo "  		  <td><input size=\"1\" type=\"text\" readonly name=\"meteo1[$id]\" value=\"".$eq1Meteo."\"></td>\n";
            }
            echo "  		  <td></td>\n";

            if($donnees['resultat']!="") echo "<td>".$eq2Meteo."</td>\n";
            else {
                echo "  		  <td><input size=\"1\" type=\"text\" readonly name=\"meteo2[$id]\" value=\"".$eq2Meteo."\"></td>\n";
            }
            
            echo "          </tr>\n";
            
            echo "  		<tr>\n";
            echo "  		  <td>Joueurs clés</td>\n";
            if($donnees['resultat']!="") echo "<td>".$donnees['joueurs_cles1']."</td>\n";
            else echo "  		  <td><input size=\"1\" type=\"number\" name=\"joueurs_cles1[$id]\" value=\"".$donnees['joueurs_cles1']."\" placeholder=\"0\"></td>\n";
            echo "  		  <td></td>\n";
            if($donnees['resultat']!="") echo "<td>".$donnees['joueurs_cles2']."</td>\n";
            else echo "  		  <td><input size=\"1\" type=\"number\" name=\"joueurs_cles2[$id]\" value=\"".$donnees['joueurs_cles2']."\" placeholder=\"0\"></td>\n";
            echo "          </tr>\n";
            
            echo "  		<tr>\n";
            echo "  		  <td>Valeur marchande</td>\n";
            if($donnees['resultat']!="") echo "<td>".$donnees['valeur_marchande1']."</td>\n";
            else echo "  		  <td><input size=\"1\" type=\"text\" readonly name=\"valeur_marchande1[$id]\" value=\"".$vm1."\"></td>\n";
            echo "  		  <td></td>\n";
            if($donnees['resultat']!="") echo "<td>".$donnees['valeur_marchande2']."</td>\n";
            else echo "  		  <td><input size=\"1\" type=\"text\" readonly name=\"valeur_marchande2[$id]\" value=\"".$vm2."\"></td>\n";
            echo "          </tr>\n";
            
            echo "  		<tr>\n";
            echo "  		  <td>Domicile / Extérieur</td>";
            if($donnees['resultat']!="") echo "<td>".$donnees['domicile_exterieur1']."</td>\n";
            else echo "  		  <td><input size=\"1\" type=\"text\" readonly name=\"domicile_exterieur1[$id]\" value=\"".$dom."\"></td>\n";
            echo "  		  <td></td>\n";
            if($donnees['resultat']!="") echo "<td>".$donnees['domicile_exterieur2']."</td>\n";
            else echo "  		  <td><input size=\"1\" type=\"text\" readonly name=\"domicile_exterieur2[$id]\" value=\"".$ext."\"></td>\n";
            echo "          </tr>\n";

            echo "          <tr>\n";
            echo "            <td>Matchs similaires</td>\n";
            echo "            <td>$similairesDom</td>\n";
            echo "            <td>$similairesNul</td>\n";
            echo "            <td>$similairesExt</td>\n";
            echo "          </tr>\n";
            
            echo "  		<tr>\n";
            echo "  		  <td><strong>Total</strong></td>\n";
            echo "  		  <td><strong>$total1</strong></td>\n";
            echo "  		  <td></td>\n";
            echo "  		  <td><strong>$total2</strong></td>\n";
            echo "          </tr>\n";

            echo "  		<tr>\n";
            echo "  		  <td>Pronostic</td>\n";
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
            
            if($donnees['resultat']!=""){
                echo "  		<tr>\n";
                echo "  		  <td>Résultat</td>\n";
                echo "  		  <td><input type=\"radio\"  readonly id=\"1\" value=\"1\"";
                if($donnees['resultat']=="1") echo " checked";
                echo "></td>\n";
                echo "  		  <td><input type=\"radio\"  readonly id=\"N\" value=\"N\"";
                if($donnees['resultat']=="N") echo " checked";
                echo "></td>\n";
                echo "  		  <td><input type=\"radio\"  readonly id=\"2\" value=\"2\"";
                if($donnees['resultat']=="2") echo " checked";
                echo "></td>\n";
                echo "          </tr>\n";
            }
            echo "	 </table>\n";
        }
        $reponse->closeCursor(); // Termine le traitement de la requête

        echo "      <div><input type=\"submit\"></div>\n";
        echo "	 </form>\n";
        
         $txtProno.= "  </table>\n";  
        echo $txtProno;

?>  
