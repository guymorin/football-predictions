<?php
include("champ_nav.php");
?>

    <section>
<?php
$idChampionnat=0;
$nomChampionnat="";
if(isset($_POST['id_championnat'])) $idChampionnat=$_POST['id_championnat'];
if(isset($_POST['nom'])) $nomChampionnat=$_POST['nom'];

$cree=0;
$modifie=0;
$supprime=0;
$sortie=0;
if(isset($_GET['cree'])) $cree=$_GET['cree'];
if(isset($_POST['cree'])) $cree=$_POST['cree'];
if(isset($_POST['modifie'])) $modifie=$_POST['modifie'];
if(isset($_POST['supprime'])) $supprime=$_POST['supprime'];
if(isset($_GET['sortie'])) $sortie=$_GET['sortie'];

$domicile=$exterieur=0;
if(isset($_GET['domicile'])) $domicile=$_GET['domicile'];
if(isset($_GET['exterieur'])) $exterieur=$_GET['exterieur'];

// SORTIR DU CHAMPIONNAT
if($sortie==1){
    unset($_SESSION['idChampionnat']);
    unset($_SESSION['nomChampionnat']);
    unset($_SESSION['idJournee']);
    unset($_SESSION['numJournee']);
    popup("Sortie du championnat.","index.php?page=championnats");
}

// SUPPRIMER
elseif($supprime==1){
        $req="DELETE FROM championnats WHERE id_championnat='".$idChampionnat."';";
        $bdd->exec($req);
        $bdd->exec("ALTER TABLE championnats AUTO_INCREMENT=0;");
        popup("Suppression du championnat.","index.php?page=championnats");
}

// CREER
elseif($cree==1){

    echo "<h2>Créer un championnat</h2>\n";

    // S'il y a une création
    if($nomChampionnat!="") {
        $bdd->exec("ALTER TABLE championnats AUTO_INCREMENT=0;");
        $req="INSERT INTO championnats VALUES(NULL,'".$nomChampionnat."');";
        $bdd->exec($req);
        popup("Création du championnat.","index.php?page=championnats");
    } else {
    
	echo "	    <form action=\"index.php?page=championnats\" method=\"POST\">\n";
    echo "      <input type=\"hidden\" name=\"cree\" value=\"1\">\n"; 
	echo "	    <label>Nom</label>\n";
	echo "     <input type=\"text\" name=\"nom\" value=\"".$nomChampionnat."\">\n";
	echo "     <input type=\"submit\" value=\"Créer\">\n";

	echo "	    </form>\n";   

	}
	
}
// MODIFIER
elseif($modifie==1){

    echo "<h2>Modifier un championnat</h2>\n";

    // S'il y a une modification
    if($nomChampionnat!="") {
        $req="UPDATE championnats SET nom='".$nomChampionnat."' WHERE id_championnat='".$idChampionnat."';";
        $bdd->exec($req);
        popup("Modification du championnat.","index.php?page=championnats");
    } else {
    
    // Affichage du championnat sélectionnée
    

    $reponse = $bdd->query("SELECT * FROM championnats WHERE id_championnat='".$idChampionnat."';");
    echo "	 <form action=\"index.php?page=championnats\" method=\"POST\">\n";
    $donnees = $reponse->fetch();

    echo "      <input type=\"hidden\" name=\"modifie\" value=1>\n";    
    
    echo "	 <label>Id.</label>\n";
    echo "      <input type=\"text\" name=\"id_championnat\" readonly=\"readonly\" value=\"".$donnees['id_championnat']."\">\n";

    echo "	 <label>Nom</label>\n";
    echo "      <input type=\"text\" name=\"nom\" value=\"".$donnees['nom']."\">\n";
    echo "      <input type=\"submit\" value=\"Modifier\">\n";
    echo "	 </form>\n";
    
    echo "	 <form action=\"index.php?page=championnats\" method=\"POST\" onsubmit=\"return(confirm('Supprimer ".$donnees['nom']." ?'))\">\n";
    echo "      <input type=\"hidden\" name=\"supprime\" value=1>\n";
    echo "      <input type=\"hidden\" name=\"id_championnat\" value=$idChampionnat>\n";
    echo "      <input type=\"hidden\" name=\"nom\" value=\"".$donnees['nom']."\">\n";
    echo "      <input type=\"submit\" value=\"&#9888 Supprimer ".$donnees['nom']." &#9888\">\n"; // Bouton Supprimer
    echo "	 </form>\n";
    $reponse->closeCursor(); // Termine le traitement de la requête   
    }
}
elseif(isset($_SESSION['idChampionnat'])&&($sortie==0)){

    echo "<h2>Championnats</h2>\n";
    echo "<h3>Classement de ".$_SESSION['nomChampionnat']."</h3>\n";
    echo "<div id=\"classement\">\n";
    echo "<ul>";
    echo "  <li>";
    if($domicile+$exterieur==0) echo "<p>Général</p>";
    else echo "<a href=\"index.php?page=championnats\">Général</a>";
    echo "  </li>\n\t<li>";
    if($domicile==1) echo "<p>Domicile</p>";
    else echo "<a href=\"index.php?page=championnats&domicile=1\">Domicile</a>";
    echo "  </li>\n\t<li>";
    if($exterieur==1) echo "<p>Extérieur</p>";
    else echo "<a href=\"index.php?page=championnats&exterieur=1\">Extérieur</a>\n";
    echo "  </li>\n</ul>\n";
    
    echo "    <table>\n";
    echo "      <tr>\n";
    echo "            <th> </th>\n";
    echo "            <th>&Eacute;quipe</th>\n";
    echo "            <th>Pts</th>\n";
    echo "            <th>J</th>\n";
    echo "            <th>G</th>\n";
    echo "            <th>N</th>\n";
    echo "            <th>P</th>\n";
    echo "      </tr>\n";

    $req="SELECT c.id_club, c.nom, COUNT(m.id_match) as matchs,
		SUM(";
    if($exterieur==0){
            $req.="CASE WHEN m.resultat = '1' AND m.equipe_1=c.id_club THEN 3 ELSE 0 END +
			CASE WHEN m.resultat = 'N' AND m.equipe_1=c.id_club THEN 1 ELSE 0 END +";
	}
	if($domicile==0){
			$req.="CASE WHEN m.resultat = '2' AND m.equipe_2=c.id_club THEN 3 ELSE 0 END +
			CASE WHEN m.resultat = 'N' AND m.equipe_2=c.id_club THEN 1 ELSE 0 END +";
    }
    $req.="0) as points,";
	$req.="	SUM(";
	if($exterieur==0){
            $req.="CASE WHEN m.resultat = '1' AND m.equipe_1=c.id_club THEN 1 ELSE 0 END +
            ";
    }
    if($domicile==0){
        	$req.="CASE WHEN m.resultat = '2' AND m.equipe_2=c.id_club THEN 1 ELSE 0 END +";
    }
    $req.="0) as gagne,";
    $req.="SUM(";
    if($exterieur==0){
			$req.="CASE WHEN m.resultat = 'N' AND m.equipe_1=c.id_club THEN 1 ELSE 0 END +";
    }
    if($domicile==0){
			$req.="CASE WHEN m.resultat = 'N' AND m.equipe_2=c.id_club THEN 1 ELSE 0 END +";
    }
    $req.="0) as nul,";
	$req.="SUM(";
    if($exterieur==0){
        	$req.="CASE WHEN m.resultat = '2' AND m.equipe_1=c.id_club THEN 1 ELSE 0 END +";
    }
	if($domicile==0){
			$req.="CASE WHEN m.resultat = '1' AND m.equipe_2=c.id_club THEN 1 ELSE 0 END +";
    }
    $req.="0) as perdu 
    FROM clubs c 
    LEFT JOIN saison_championnat_club scc ON c.id_club=scc.id_club 
    LEFT JOIN journees j ON (scc.id_saison=j.id_saison AND scc.id_championnat=j.id_championnat) 
    LEFT JOIN matchs m ON m.id_journee=j.id_journee 
    WHERE scc.id_saison='".$_SESSION['idSaison']."' 
    AND scc.id_championnat='".$_SESSION['idChampionnat']."' 
    AND (c.id_club=m.equipe_1 OR c.id_club=m.equipe_2) 
    AND m.resultat<>'' 
    GROUP BY c.id_club,c.nom 
    ORDER BY points DESC, c.nom ASC;";
    $reponse = $bdd->query($req);
    $compteur=0;
    $pointsPrec=0;
    // On affiche chaque entrée
    while ($donnees = $reponse->fetch())
    {
        echo "        <tr>\n";
        echo "          <td>";
        if($donnees['points']!=$pointsPrec){
            $compteur++;
            echo $compteur;
            $pointsPrec=$donnees['points'];
        }
        echo "</td>\n";
        echo "          <td>".$donnees['nom']."</td>\n";
        echo "          <td>".$donnees['points']."</td>\n";
        echo "          <td>".$donnees['matchs']."</td>\n";
        echo "          <td>".$donnees['gagne']."</td>\n";
        echo "          <td>".$donnees['nul']."</td>\n";
        echo "          <td>".$donnees['perdu']."</td>\n";
        echo "        </tr>\n";
    }
    $reponse->closeCursor(); // Termine le traitement de la requête   
    
}
?>
        </table>
    </div>
    </section>
    
