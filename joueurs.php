<?php
// NAVIGATION JOUEUR
echo "  <nav>\n";
$reponse = $bdd->query("SELECT * FROM joueurs ORDER BY nom, prenom");
echo "  	<a href=\"/\">&#8617</a>\n";                                   // Retour
echo "  	<a href=\"index.php?page=joueurs\">Meilleurs joueurs</a>\n"; // Créer
echo "  	<a href=\"index.php?page=joueurs&cree=1\">Créer un joueur</a>\n"; // Créer
echo "  	<a href=\"index.php?page=joueurs&modifie=1\">Modifier un joueur</a>\n"; // Modifier
echo "      </nav>\n";
$reponse->closeCursor(); // Termine le traitement de la requête

?>

    <section>
<?php
$idJoueur=0;
$nomJoueur="";
$prenomJoueur="";
$posteJoueur="";
$idClub=0;
if(isset($_POST['id_joueur'])) $idJoueur=$_POST['id_joueur'];
if(isset($_POST['nom'])) $nomJoueur=$_POST['nom'];
if(isset($_POST['prenom'])) $prenomJoueur=$_POST['prenom'];
if(isset($_POST['poste'])) $posteJoueur=$_POST['poste'];
if(isset($_POST['id_club'])) $idClub=$_POST['id_club'];

$cree=0;
$modifie=0;
$supprime=0;
if(isset($_GET['cree'])) $cree=$_GET['cree'];
if(isset($_POST['cree'])) $cree=$_POST['cree'];
if(isset($_GET['modifie'])) $modifie=$_GET['modifie'];
if(isset($_POST['modifie'])) $modifie=$_POST['modifie'];
if(isset($_POST['supprime'])) $supprime=$_POST['supprime'];

// SUPPRIMER
if($supprime==1){
        $req="DELETE FROM saison_club_joueur WHERE id_saison='".$_SESSION['idSaison']."' AND id_club='".$idClub."' AND id_joueur='".$idJoueur."';";
        $req.="DELETE FROM joueurs WHERE id_joueur='".$idJoueur."';";
        $bdd->exec($req);
        $bdd->exec("ALTER TABLE saison_club_joueur AUTO_INCREMENT=0");
        $bdd->exec("ALTER TABLE joueurs AUTO_INCREMENT=0");
        popup("Suppression pour ".mb_strtoupper($nomJoueur,'UTF-8')." ".$prenomJoueur.".","index.php?page=joueurs");
}

// CREER
elseif($cree==1){

    echo "<h2>Créer un joueur</h2>\n";

    // S'il y a une création
    if($nomJoueur!="") {
        $bdd->exec("ALTER TABLE saison_club_joueur AUTO_INCREMENT=0;");
        $bdd->exec("ALTER TABLE joueurs AUTO_INCREMENT=0;");
        $req1="INSERT INTO joueurs VALUES(NULL,'".$nomJoueur."','".$prenomJoueur."','".$posteJoueur."');";
        $bdd->exec($req1);
        $idJoueur=$bdd->lastInsertId();
        $req2="INSERT INTO saison_club_joueur VALUES(NULL,'".$_SESSION['idSaison']."','".$idClub."','".$idJoueur."');";
        $bdd->exec($req2);
        popup("Création pour ".mb_strtoupper($nomJoueur,'UTF-8')." ".$prenomJoueur.".","index.php?page=joueurs");
    } else {
    
	echo "	    <form action=\"index.php?page=joueurs\" method=\"POST\">\n";
    echo "      <input type=\"hidden\" name=\"cree\" value=\"1\">\n"; 
    
	echo "	    <label>Nom</label>\n";
	echo "     <input type=\"text\" name=\"nom\" value=\"".$nomJoueur."\">\n";
	echo "	    <label>Prénom</label>\n";
	echo "     <input type=\"text\" name=\"prenom\" value=\"".$nomJoueur."\">\n";
	
	echo "	    <p><label>Poste</label>\n";
	echo "     <input type=\"radio\" name=\"poste\" id=\"Gardien\" value=\"Gardien\"><label for=\"Gardien\">Gardien</label>\n";
	echo "     <input type=\"radio\" name=\"poste\" id=\"Défenseur\" value=\"Défenseur\"><label for=\"Défenseur\">Défenseur</label>\n";
	echo "     <input type=\"radio\" name=\"poste\" id=\"Milieu\" value=\"Milieu\"><label for=\"Milieu\">Milieu</label>\n";
	echo "     <input type=\"radio\" name=\"poste\" id=\"Attaquant\" value=\"Attaquant\"><label for=\"Attaquant\">Attaquant</label></p>\n";
	
    echo "	    <p><label>Club</label>\n";
    echo "     <select multiple size=\"10\" name=\"id_club\">\n";
    // On affiche chaque entrée
    $reponse = $bdd->query("SELECT * FROM clubs ORDER BY nom;");
    while ($donnees = $reponse->fetch())
    {
        echo "  		<option value=\"".$donnees['id_club']."\">".$donnees['nom']."</option>\n";
    }
    echo "	 </select></p>\n";
    
	echo "     <input type=\"submit\" value=\"Créer\">\n";
	echo "	    </form>\n";   

	}
	
}
// MODIFIER
elseif($modifie==1){

    echo "<h2>Modifier un joueur</h2>\n";

    // S'il y a une modification
    if($nomJoueur!="") {
        $req="UPDATE joueurs SET nom='".$nomJoueur."', prenom='".$prenomJoueur."' WHERE id_joueur='".$idJoueur."';";
        
        $reponse = $bdd->query("SELECT COUNT(*) as nb FROM saison_club_joueur WHERE id_saison='".$_SESSION['idSaison']."' AND id_club='".$idClub."' AND id_joueur='".$idJoueur."';");
        $donnees = $reponse->fetch();
        $reponse->closeCursor(); // Termine le traitement de la requête
        
        if($donnees[0]==0){
            $req.="INSERT INTO saison_club_joueur VALUES(NULL,'".$_SESSION['idSaison']."','".$idClub."','".$idJoueur."');";
        }
        if($donnees[0]==1){
            $req.="UPDATE saison_club_joueur SET id_saison='".$_SESSION['idSaison']."',id_club='".$idClub."' WHERE id_joueur='".$idJoueur."';";
        }
        $bdd->exec($req);
        popup("Modification pour ".mb_strtoupper($nomJoueur,'UTF-8')." ".$prenomJoueur.".","index.php?page=joueurs");
    } elseif($idJoueur!=0) {
    
    // Si un joueur est sélectionné alors affichage
    $req ="SELECT j.id_joueur, j.nom, j.prenom, j.poste, scj.id_club FROM joueurs j LEFT JOIN saison_club_joueur scj ON j.id_joueur=scj.id_joueur LEFT JOIN clubs c ON scj.id_club=c.id_club WHERE j.id_joueur='".$idJoueur."';";
    $reponse = $bdd->query($req);
    
    echo "	 <form action=\"index.php?page=joueurs\" method=\"POST\">\n";
    $donnees = $reponse->fetch();
    $idJoueur = $donnees['id_joueur'];
    $nomJoueur = $donnees['nom'];
    $prenomJoueur = $donnees['prenom'];
    $idClub = $donnees['id_club'];
    
    echo "      <input type=\"hidden\" name=\"modifie\" value=1>\n";    
    
    echo "	 <label>Id.</label>\n";
    echo "      <input type=\"text\" name=\"id_joueur\" readonly value=\"".$idJoueur."\">\n";

    echo "	 <label>Nom</label>\n";
    echo "      <input type=\"text\" name=\"nom\" value=\"".$nomJoueur."\">\n";
    echo "	 <label>Prénom</label>\n";
    echo "      <input type=\"text\" name=\"prenom\" value=\"".$prenomJoueur."\">\n";
    
	echo "	    <p><label>Poste</label>\n";
	echo "     <input type=\"radio\" name=\"poste\" id=\"Gardien\" value=\"Gardien\"";
        if ($donnees['poste']=="Gardien") echo " checked";
	echo "><label for=\"Gardien\">Gardien</label>\n";
	echo "     <input type=\"radio\" name=\"poste\" id=\"Défenseur\" value=\"Défenseur\"";
        if ($donnees['poste']=="Défenseur") echo " checked";
	echo "><label for=\"Défenseur\">Défenseur</label>\n";
	echo "     <input type=\"radio\" name=\"poste\" id=\"Milieu\" value=\"Milieu\"";
        if ($donnees['poste']=="Milieu") echo " checked";	
	echo "><label for=\"Milieu\">Milieu</label>\n";
	echo "     <input type=\"radio\" name=\"poste\" id=\"Attaquant\" value=\"Attaquant\"";
        if ($donnees['poste']=="Attaquant") echo " checked";	
	echo "><label for=\"Attaquant\">Attaquant</label></p>\n";
	
    echo "	    <p><label>Club</label>\n";
    echo "     <select multiple size=\"10\" name=\"id_club\">\n";
    // On affiche chaque entrée
    $reponse = $bdd->query("SELECT * FROM clubs ORDER BY nom;");
    while ($donnees = $reponse->fetch())
    {
        echo "  		<option value=\"".$donnees['id_club']."\"";
        if($donnees['id_club']==$idClub) echo " selected";
        echo ">".$donnees['nom']."</option>\n";
    }
    echo "	 </select></p>\n";
	
    echo "      <input type=\"submit\" value=\"Modifier\">\n";
    echo "	 </form>\n";
    
    echo "	 <form action=\"index.php?page=joueurs\" method=\"POST\" onsubmit=\"return(confirm('Supprimer ".mb_strtoupper($nomJoueur,'UTF-8')." ".$prenomJoueur." ?'))\">\n";
    echo "      <input type=\"hidden\" name=\"supprime\" value=1>\n";
    echo "      <input type=\"hidden\" name=\"id_club\" value=$idClub>\n";
    echo "      <input type=\"hidden\" name=\"id_joueur\" value=$idJoueur>\n";
    echo "      <input type=\"hidden\" name=\"nom\" value=\"".$nomJoueur."\">\n";
    echo "      <input type=\"hidden\" name=\"prenom\" value=\"".$prenomJoueur."\">\n";
    echo "      <input type=\"submit\" value=\"&#9888 Supprimer ".mb_strtoupper($nomJoueur,'UTF-8')." ".$prenomJoueur." &#9888\">\n"; // Bouton Supprimer
    echo "	 </form>\n";
    $reponse->closeCursor(); // Termine le traitement de la requête   
    
    } else {
    
    // Sinon affichage de tous les joueurs
    echo "  	<form action=\"index.php?page=joueurs\" method=\"POST\">\n";             // Modifier
    echo "      <input type=\"hidden\" name=\"modifie\" value=\"1\">\n"; 
    echo "    <label>Choisir un joueur :</label>\n";                                    
    echo "  	<select multiple size=\"10\" name=\"id_joueur\">\n";
    // On affiche chaque entrée
    $reponse = $bdd->query("SELECT * FROM joueurs ORDER BY nom, prenom");
    while ($donnees = $reponse->fetch())
    {
        echo "  		<option value=\"".$donnees['id_joueur']."\">".mb_strtoupper($donnees['nom'],'UTF-8')." ".$donnees['prenom']."</option>\n";
    }
    echo "	 </select>\n";
    echo "      <input type=\"submit\">\n";
    echo "	 </form>\n";
    }
} else {

echo "<h2>Joueurs</h2>\n";
echo "<h3>Meilleurs joueurs du championnat</h3>\n";

$req = "SELECT COUNT(e.note) as nb,AVG(e.note) as note,c.nom as club,j.nom,j.prenom 
FROM joueurs j
LEFT JOIN saison_club_joueur scj ON j.id_joueur=scj.id_joueur 
LEFT JOIN clubs c ON  c.id_club=scj.id_club 
LEFT JOIN equipes e ON e.id_joueur=j.id_joueur 
GROUP BY club, j.nom,j.prenom 
ORDER BY nb DESC, note DESC,j.nom,j.prenom LIMIT 0,3";
$reponse = $bdd->query($req);
echo "  <p><table>\n";
echo "      <tr><th></th><th>Joueur</th><th>Club</th><th>&Eacute;quipe type</th><th>Note moyenne</th></tr>\n";
$compteurPodium = 0;
$icone = "&#129351;"; // or
while ($donnees = $reponse->fetch())
    {
    $compteurPodium++;
    if($compteurPodium==2) $icone="&#129352;"; // argent
    else $icone="&#129353;"; // bronze
    
        echo "      <td><strong>".$compteurPodium."</strong></td>\n";
        echo "      <td>".$icone." <strong>".mb_strtoupper($donnees['nom'],'UTF-8')." ".$donnees['prenom']."</strong></td>\n";
        echo "      <td>".$donnees['club']."</td>\n";
        echo "      <td>".$donnees['nb']."</td>\n";
        echo "      <td>".round($donnees['note'],1)."</td>\n";
        echo "  </tr>\n";
    }
echo "  </table></p>\n";
$reponse->closeCursor(); // Termine le traitement de la requête   

echo "<h3>Meilleurs joueurs par club</h3>\n";

$req = "SELECT COUNT(e.note) as nb,AVG(e.note) as note,c.nom as club,j.nom,j.prenom 
FROM joueurs j
LEFT JOIN saison_club_joueur scj ON j.id_joueur=scj.id_joueur 
LEFT JOIN clubs c ON  c.id_club=scj.id_club 
LEFT JOIN equipes e ON e.id_joueur=j.id_joueur 
GROUP BY club,j.nom,j.prenom 
ORDER BY club ASC, nb DESC, note DESC, j.nom,j.prenom ASC";
$reponse = $bdd->query($req);
echo "  <table>\n";
echo "      <tr><th>Club</th><th>Joueur</th><th>&Eacute;quipe type</th><th>Note moyenne</th></tr>\n";
$compteur = "";
while ($donnees = $reponse->fetch())
    {
        echo "      <td>";
        if($compteur!=$donnees['club']){
            $compteurPodium = 0;
            echo "<strong>".$donnees['club']."</strong>";
        }
        
        $compteurPodium++;
        switch($compteurPodium){
            case 1:
                $icone = "&#129351;"; // or
                break;
            case 2:
                $icone="&#129352;"; // argent
                break;
            case 3:
                $icone="&#129353;"; // bronze
                break;
            default:
                $icone="";
        }
        
        echo "</td><td>";
        if($icone!="") echo $icone." <strong>".mb_strtoupper($donnees['nom'],'UTF-8')." ".$donnees['prenom']."</strong>";
        else echo mb_strtoupper($donnees['nom'],'UTF-8')." ".$donnees['prenom'];
        echo "</td><td>".$donnees['nb']."</td><td>".round($donnees['note'],1)."</td>\n";
        echo "  </tr>\n";
        $compteur=$donnees['club'];
    }
$reponse->closeCursor(); // Termine le traitement de la requête   
echo "  </table>\n";
}
?>
    </section>
    
