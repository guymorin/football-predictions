<?php
include("inc_changeMD.php");
// NAVIGATION JOURNEES
include("matchday_nav.php");
?>

    <section>
<?php

$idJournee=0;
$numJournee="";

if(isset($_POST['choixJournee'])){
        $v=explode(",",$_POST['choixJournee']);
        $idJournee=$v[0];
}
if(isset($_POST['id_journee'])) $idJournee=$_POST['id_journee'];
if(isset($_POST['numero'])) $numJournee=$_POST['numero'];

$cree=0;
$modifie=0;
$supprime=0;
$equipe=0;
$sortie=0;
if(isset($_GET['cree'])) $cree=$_GET['cree'];
if(isset($_POST['cree'])) $cree=$_POST['cree'];
if(isset($_POST['modifie'])) $modifie=$_POST['modifie'];
if(isset($_POST['supprime'])) $supprime=$_POST['supprime'];
if(isset($_POST['equipe'])) $equipe=$_POST['equipe'];
if(isset($_GET['sortie'])) $sortie=$_GET['sortie'];

$idJoueur=0;
$noteJoueur=0;
$delJoueur=0;
if(isset($_POST['id_joueur'])) $idJoueur=$_POST['id_joueur'];
if(isset($_POST['note'])) $noteJoueur=$_POST['note'];
if(isset($_POST['delete'])) $delJoueur=$_POST['delete'];
$val=array_combine($idJoueur,$noteJoueur);



// SORTIR DE LA JOURNEE
if($sortie==1){
    unset($_SESSION['idJournee']);
    unset($_SESSION['numJournee']);
    popup("Sortie de la journée","index.php");
}

// STATISTIQUES
if(isset($_SESSION['idJournee'])){


    // MAJ DES NOTES
    if($equipe==1){
        // On met à jour
        $bdd->exec("ALTER TABLE equipes AUTO_INCREMENT=0;");
        $req="";

        foreach($delJoueur as $d){
            $req="DELETE FROM equipes WHERE id_journee='".$_SESSION['idJournee']."' AND id_joueur='".$d."';";
            $bdd->exec($req);
        }
        
        $bdd->exec("ALTER TABLE equipes AUTO_INCREMENT=0;");
        $req="";
        foreach($val as $k=>$v){
            if(($v!="")&&(!in_array($k,$delJoueur))){
            
                $reponse = $bdd->query("SELECT COUNT(*) as nb FROM equipes WHERE id_journee='".$_SESSION['idJournee']."' AND id_joueur='".$k."';");
                $donnees = $reponse->fetch();
                $reponse->closeCursor(); // Termine le traitement de la requête
                
                if($donnees[0]==0) {
                    $req.="INSERT INTO equipes VALUES(NULL,'".$_SESSION['idJournee']."','".$k."','".$v."');";
                }
                if($donnees[0]==1) {
                    $req.="UPDATE equipes SET note='".$v."' WHERE id_journee='".$_SESSION['idJournee']."' AND id_joueur='".$k."';";
                }
            
            }
        } 
        $bdd->exec($req);
        popup("Modification de l'équipe type.","index.php?matchday");
    
    } else {
        
        // STATISTIQUES
        
        

changeJ($bdd,"Statistiques","journees");



        $req="SELECT m.id_match,
        cr.motivation_confiance1,cr.motivation_confiance2,
        cr.serie_en_cours1,cr.serie_en_cours2,
        cr.forme_physique1,cr.forme_physique2,
        cr.meteo1,cr.meteo2,
        cr.joueurs_cles1,cr.joueurs_cles2,
        cr.valeur_marchande1,cr.valeur_marchande2,
        cr.domicile_exterieur1,cr.domicile_exterieur2,
        c1.nom as nom1,c2.nom as nom2,c1.id_club as eq1,c2.id_club as eq2,
        m.resultat, m.date, m.cote1, m.coteN, m.cote2 FROM matchs m 
        LEFT JOIN clubs c1 ON m.equipe_1=c1.id_club 
        LEFT JOIN clubs c2 ON m.equipe_2=c2.id_club 
        LEFT JOIN criteres cr ON cr.id_match=m.id_match 
        WHERE m.id_journee='".$_SESSION['idJournee']."' ORDER BY m.date 
        ;";
        $reponse = $bdd->query($req);

        $table="	 <table class=\"stats\">\n";
           
        $table.="  		<tr>\n";
        $table.="  		  <th>Matchs</th>\n";
        $table.="         <th>Pronostic</th>\n";
        $table.="         <th>Résultat</th>\n";
        $table.="         <th>Cote jouée</th>\n";
        $table.="         <th>Succ&egrave;s</th>\n";
        $table.="       </tr>\n";
        
        $matchs=$succes=$totalGains=$totalJouee=0;
        
        while ($donnees = $reponse->fetch())
        {
            
            // Calcul de la VM
            $v1=criteres("v1",$donnees,$bdd);
            $v2=criteres("v2",$donnees,$bdd);
            $vm1 = round(sqrt($v1/$v2));
            $vm2 = round(sqrt($v2/$v1));
            
            $dom = $donnees['domicile_exterieur1']; 
            $ext = $donnees['domicile_exterieur2']; 
            
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
                AND cr.meteo1='".$donnees['meteo1']."' 
                AND cr.meteo2='".$donnees['meteo2']."' 
                AND cr.joueurs_cles1='".$donnees['joueurs_cles1']."' 
                AND cr.joueurs_cles2='".$donnees['joueurs_cles2']."' 
                AND cr.valeur_marchande1='".$donnees['valeur_marchande1']."' 
                AND cr.valeur_marchande2='".$donnees['valeur_marchande2']."' 
                AND cr.domicile_exterieur1='".$donnees['domicile_exterieur1']."' 
                AND cr.domicile_exterieur2='".$donnees['domicile_exterieur2']."' 
                AND m.date<'".$donnees['date']."'";
                $r = $bdd->query($req)->fetch();
                $similairesDom=criteres("msDom",$r,$bdd);
                $similairesExt=criteres("msExt",$r,$bdd);
                
            // Calcul du total
            $gagne="";

            $total1=
                $donnees['motivation_confiance1']
                +$donnees['serie_en_cours1']
                +$donnees['forme_physique1']
                +$donnees['meteo1']
                +$donnees['joueurs_cles1']
                +$vm1
                +$dom
                +$similairesDom;
            $total2=
                $donnees['motivation_confiance2']
                +$donnees['serie_en_cours2']
                +$donnees['forme_physique2']
                +$donnees['meteo2']
                +$donnees['joueurs_cles2']
                +$vm2
                +$ext
                +$similairesExt;
            if($total1>$total2) $prono="1";
            elseif($total1==$total2) $prono="N";
            elseif($total1<$total2) $prono="2";
            
            $matchs++;
            
            $coteJouee=0;
            switch($prono){
                case "1":
                    $coteJouee = $donnees['cote1'];
                    break;
                case "N":
                    $coteJouee = $donnees['coteN'];
                    break;
                case "2":
                    $coteJouee = $donnees['cote2'];
                    break;
            }
            
            if($prono==$donnees['resultat']){
                $gagne="<big style=\"color:green\">&#x2714;</big>";
                $succes++;
                $totalGains+=$coteJouee;
            } elseif ($donnees['resultat']!="") $gagne="<small style=\"color:gray\">&times;</small>";
            $totalJouee+=$coteJouee;
            
       // On affiche chaque entrée
                
            $table.="  		<tr>\n";
            $table.="  		  <td>".$donnees['nom1']." - ".$donnees['nom2']."</td>\n";
            $table.="  		  <td>".$prono."</td>\n";
            $table.="  		  <td>".$donnees['resultat']."</td>\n";
            $table.="  		  <td>".$coteJouee."</td>\n";
            $table.="  		  <td>".$gagne."</td>\n";
            $table.="       </tr>\n";

        }
        $reponse->closeCursor(); // Termine le traitement de la requête
        $table.="	 </table>\n";
        
        // Calculs
        $benef=money_format('%i',$totalGains-$matchs);
        $roi = round(($benef/$matchs)*100);
        $tauxReussite = (($succes/$matchs)*100);
        $gains = money_format('%i',$totalGains);
        $gainParMise = (round($totalGains/$matchs,2));
        
        echo "<p>\n<table class=\"stats\">\n";
        echo "  <tr><th colspan=\"6\">En résumé</th></tr>\n";
        echo "  <tr>\n";
        echo "      <td>Mises</td><td>".$matchs."</td>\n";
        
        // Bénéfice
        echo "      <td>Bénéfice</td><td><span style=\"color:".valColor($benef)."\">";
        if($benef>0) echo "+";
        echo $benef."&nbsp;&euro;</span></td>\n";

        
        // ROI
        echo "      <td>ROI</td>\n";
        echo "<td>";
        echo "<span style=\"color:".valColor($roi)."\">";
        if($roi>0) echo "+";
        echo $roi."&nbsp;%</span>";
        echo "&nbsp;<a href=\"#\" class=\"infobulle\">&#128172;<span>Le&nbsp;ROI&nbsp;est&nbsp;";
        switch($roi){
            case($roi<0):
                echo "perdant";
                break;
            case($roi==0):
                echo "neutre";
                break;
            case($roi>0&&$roi<15):
                echo "gagnant";
                break;
            case($roi>=15):
                echo "excellent";
                break;
        }
        echo "&nbsp;!</span></a>";
        echo "</td>\n";

        echo "  <tr>\n";
        echo " </tr>\n";
        
        // Succès
        echo "      <td>Succ&egrave;s</td><td>".$succes."</td>\n";
        
        // Gains
        echo "      <td>Gains</td><td>".$gains."&nbsp;&euro;</td>\n";
        
        // Gains par mise
        echo "      <td>Gains&nbsp;par&nbsp;mise</td><td>".$gainParMise."</td>\n";
    

        echo " </tr>\n";
        echo "</td>\n";
        echo " </tr>\n";
        echo " </tr>\n";
        
        // Taux de réussite
        echo "<td>Taux&nbsp;de&nbsp;réussite</td><td>";
        if($matchs>0) echo $tauxReussite;
        else echo 0;
        echo "&nbsp;%</td>";

        // Cote moyenne jouée
        $coteMoy=(round($totalJouee/$matchs,2));
        echo "      <td>Cote&nbsp;moyenne&nbsp;jouée</td><td>".$coteMoy;
        if(($coteMoy<1.8)||($coteMoy>2.3)){
            echo "&nbsp;<a href=\"#\" class=\"infobulle\">&#128172;<span>Jeu&nbsp;";
            switch($coteMoy){
                case($coteMoy<1.5):
                    echo "trop prudent";
                    break;
                case($coteMoy<1.8):
                    echo "prudent";
                    break;
                case($coteMoy>3):
                    echo "trop spéculateur";
                    break;
                case($coteMoy>2.3):
                    echo "spéculateur";
                    break;
            }
            echo "&nbsp;!</span></a>";
        }
        echo "</td>\n";

        echo "      <td></td><td></td>\n";
        echo " </tr>\n";
        echo "</table>\n</p>";
        echo $table;      
        
    }
}

// SUPPRIMER
elseif($supprime==1){
        $req="DELETE FROM journees WHERE id_journee='".$idJournee."';";
        $bdd->exec($req);
        $bdd->exec("ALTER TABLE journees AUTO_INCREMENT=0;");
        popup("Suppression pour J".$numJournee.".","index.php?page=journees");
}

// CREER
elseif($cree==1){

echo "<h2>Créer une journée</h2>\n";

    // S'il y a une création
    if($numJournee!="") {
        $bdd->exec("ALTER TABLE journees AUTO_INCREMENT=0;");
        $req="INSERT INTO journees VALUES(NULL,'".$_SESSION['idSaison']."','".$_SESSION['idChampionnat']."','".$numJournee."');";
        $bdd->exec($req);
        popup("Création pour J".$numJournee.".","index.php?page=journees");
    } else {
    
	echo "	    <form action=\"index.php?page=journees\" method=\"POST\" onsubmit=\"return confirm('Attention, vous allez modifier les données !');\">\n";
    echo "      <input type=\"hidden\" name=\"cree\" value=\"1\">\n"; 
	echo "	    <label>Numéro</label>\n";
	echo "     <input type=\"text\" name=\"numero\" value=\"".$numJournee."\">\n";
	echo "     <input type=\"submit\" value=\"Créer\">\n";

	echo "	    </form>\n";   

	}
	
}
// MODIFIER
elseif($modifie==1){

echo "<h2>Modifier une journée</h2>\n";

    // S'il y a une modification
    if($numJournee!="") {
        $req="UPDATE journees SET numero='".$numJournee."' WHERE id_journee='".$idJournee."';";
        $bdd->exec($req);
        popup("Modification pour J".$numJournee.".","index.php?page=journees");
    } else {
    
    // Affichage de la journée sélectionnée

    $reponse = $bdd->query("SELECT * FROM journees WHERE id_journee='".$idJournee."';");
    echo "	 <form action=\"index.php?page=journees\" method=\"POST\" onsubmit=\"return confirm('Attention, vous allez modifier les données !');\">\n";
    $donnees = $reponse->fetch();

    echo "      <input type=\"hidden\" name=\"modifie\" value=1>\n";    
    
    echo "	 <label>Id.</label>\n";
    echo "      <input type=\"text\" name=\"id_journee\" readonly=\"readonly\" value=\"".$donnees['id_journee']."\">\n";

    echo "	 <label>Numéo</label>\n";
    echo "      <input type=\"text\" name=\"numero\" value=\"".$donnees['numero']."\">\n";
    echo "      <input type=\"submit\" value=\"Modifier\">\n";
    echo "	 </form>\n";
    
    echo "	 <form action=\"index.php?page=journees\" method=\"POST\" onsubmit=\"return(confirm('Supprimer J".$donnees['numero']." ?'))\">\n";
    echo "      <input type=\"hidden\" name=\"supprime\" value=1>\n";
    echo "      <input type=\"hidden\" name=\"id_journee\" value=$idJournee>\n";
    echo "      <input type=\"hidden\" name=\"numero\" value=\"".$donnees['numero']."\">\n";
    echo "      <input type=\"submit\" value=\"&#9888 Supprimer J".$donnees['numero']." &#9888\">\n"; // Bouton Supprimer
    echo "	 </form>\n";
    $reponse->closeCursor(); // Termine le traitement de la requête   
    }
}
?>
    </section>
    
