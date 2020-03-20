<?php
include("inc_changeJ.php");
// NAVIGATION JOURNEES
include("journees_nav.php");
?>

    <section>
<?php

// EQUIPE TYPE
if(isset($_SESSION['idJournee'])){

    changeJ($bdd,"&Eacute;quipe type","equipe_type");
    
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
        popup("Modification des joueurs l'équipe type.","index.php?page=equipe_type");
    
    } else {
        
        // On propose le formulaire
        
        $compteur=0;
        $req = "SELECT j.id_joueur,j.nom,j.prenom,e.note FROM equipes e LEFT JOIN joueurs j ON e.id_joueur=j.id_joueur WHERE id_journee='".$_SESSION['idJournee']."' ORDER BY j.poste,j.nom,j.prenom;";
        $reponse = $bdd->query($req); 
        
        echo "	 <form action=\"index.php?page=equipe_type\" method=\"POST\" onsubmit=\"return confirm('Attention, vous allez modifier les données !');\">\n";
        echo "      <input type=\"hidden\" name=\"equipe\" value=\"1\">\n";
        
        echo "  <table id=\"equipe_type\">\n";
        echo "    <tr><th> </th><th>Joueur</th><th>Note</th><th>&#10060;</th></tr>\n";
        
        // On affiche chaque entrée
        while ($donnees = $reponse->fetch())
        {
            $compteur++;
            echo "  	<tr>";
            echo "<td>".$compteur."</td>";
            echo "<td><input type=\"hidden\" name=\"id_joueur[]\" value=\"".$donnees['id_joueur']."\">".mb_strtoupper($donnees['nom'],'UTF-8')." ".$donnees['prenom']."</td>";
            echo "<td><input type=\"text\" name=\"note[]\" size=\"3\" value=\"".$donnees['note']."\"</td><td><input type=\"checkbox\" name=\"delete[]\" value=\"".$donnees['id_joueur']."\"></td></tr>\n";
        }
        
        $req = "SELECT j.id_joueur, j.nom, j.prenom, j.poste, c.nom as club 
        FROM joueurs j
        LEFT JOIN saison_club_joueur scj ON scj.id_joueur=j.id_joueur 
        LEFT JOIN saison_championnat_club scc ON scc.id_club=scj.id_club 
        LEFT JOIN clubs c ON c.id_club=scj.id_club
        WHERE scc.id_saison='".$_SESSION['idSaison']."' 
        AND scc. id_championnat='".$_SESSION['idChampionnat']."' 
        ORDER BY j.nom, j.prenom;";
       
        $reste=11-$compteur;
        for($i=0;$i<$reste;$i++){
            $compteur++;
            $reponse = $bdd->query($req);
            echo "  	<tr>";
            echo "<td>".$compteur."</td>";
            echo "<td><select name=\"id_joueur[]\">\n";
            echo "  <option value=\"\">...</option>\n";
            // On affiche chaque entrée
            while ($donnees = $reponse->fetch())
            {

                echo "  <option value=\"".$donnees['id_joueur']."\">".mb_strtoupper($donnees['nom'],'UTF-8')." ".$donnees['prenom']." [".$donnees['club']."]";
                echo "</option>\n";
            }
            echo "</select>\n";
            echo "</td><td><input type=\"text\" name=\"note[]\" value=\"\"></td><td> </td></tr>\n";
        }
        echo "  </table>\n";
        echo "      <input type=\"submit\">\n";
        echo "	 <form>\n";
        $reponse->closeCursor(); // Termine le traitement de la requête   
    
    }
}

?>
    </section>
    
