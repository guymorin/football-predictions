<?php
// NAVIGATION CLUB
echo "  <nav>\n";

echo "  	<a href=\"/\">&#8617</a>\n";                                   // Retour
echo "	<a href=\"index.php?page=valeurs\">Valeurs</a>\n"; // Valeurs
echo "  	<a href=\"index.php?page=clubs&cree=1\">Créer un club</a>\n"; // Créer

echo "  	<form action=\"index.php?page=clubs\" method=\"POST\">\n";             // Modifier
echo "      <input type=\"hidden\" name=\"modifie\" value=\"1\">\n"; 
echo "    <label>Modifier un club :</label>\n";                                    
echo "  	<select name=\"id_club\" onchange=\"submit()\">\n";

$reponse = $bdd->query("SELECT c.* FROM clubs c LEFT JOIN saison_championnat_club scc ON c.id_club=scc.id_club WHERE scc.id_saison='".$_SESSION['idSaison']."' AND scc.id_championnat='".$_SESSION['idChampionnat']."' ORDER BY nom;");
include("clubs_select.php");
$reponse->closeCursor(); // Termine le traitement de la requête

echo "	 </select>\n";
echo "      <noscript><input type=\"submit\"></noscript>\n";

echo "	 </form>\n";
echo "      </nav>\n";

?>

    <section>
<?php
$idClub=0;
$nomClub="";
$codeInsee=0;
if(isset($_POST['id_club'])) $idClub=$_POST['id_club'];
if(isset($_POST['nom'])) $nomClub=$_POST['nom'];
if(isset($_POST['code_insee'])) $codeInsee=$_POST['code_insee'];

$cree=0;
$modifie=0;
$supprime=0;
if(isset($_GET['cree'])) $cree=$_GET['cree'];
if(isset($_POST['cree'])) $cree=$_POST['cree'];
if(isset($_POST['modifie'])) $modifie=$_POST['modifie'];
if(isset($_POST['supprime'])) $supprime=$_POST['supprime'];

// SUPPRIMER
if($supprime==1){
        $req="DELETE FROM clubs WHERE id_club='".$idClub."';";
        $bdd->exec($req);
        $bdd->exec("ALTER TABLE clubs AUTO_INCREMENT=0;");
        popup("Suppression du club ".$nomClub.".","index.php?page=clubs");
}

// CREER
if($cree==1){

echo "<h2>Créer un club</h2>\n";

    // S'il y a une création
    if($nomClub!="") {
        $bdd->exec("ALTER TABLE clubs AUTO_INCREMENT=0;");
        $req="INSERT INTO clubs VALUES(NULL,'".$nomClub."','".$codeInsee."');";
        $bdd->exec($req);
        popup("Création du club ".$nomClub.".","index.php?page=clubs");
    } else {
    
	echo "	    <form action=\"index.php?page=clubs\" method=\"POST\">\n";
    echo "      <input type=\"hidden\" name=\"cree\" value=\"1\">\n"; 
	echo "	    <label>Nom</label>\n";
	echo "     <input type=\"text\" name=\"nom\" value=\"".$nomClub."\">\n";
	echo "     <input type=\"submit\" value=\"Créer\">\n";

	echo "	    </form>\n";   

	}
	
}
// MODIFIER
if($modifie==1){

echo "<h2>Modifier un club</h2>\n";

    // S'il y a une modification
    if($nomClub!="") {
        $req="UPDATE clubs SET nom='".$nomClub."', code_insee='".$codeInsee."' WHERE id_club='".$idClub."';";
        $bdd->exec($req);
        popup("Modification du club ".$nomClub.".","index.php?page=clubs");
    } else {
    
    // Affichage du club sélectionné

    $reponse = $bdd->query("SELECT * FROM clubs WHERE id_club='".$idClub."';");
    echo "	 <form action=\"index.php?page=clubs\" method=\"POST\">\n";
    $donnees = $reponse->fetch();

    echo "      <input type=\"hidden\" name=\"modifie\" value=1>\n";    
    
    echo "	 <label>Id.</label>\n";
    echo "      <input type=\"text\" name=\"id_club\" readonly=\"readonly\" value=\"".$donnees['id_club']."\">\n";
    echo "	 <label>Nom</label>\n";
    echo "      <input type=\"text\" name=\"nom\" value=\"".$donnees['nom']."\">\n";
    echo "	 <label>Code Insee</label>\n";
    echo "      <input type=\"text\" name=\"code_insee\" value=\"".$donnees['code_insee']."\">\n";
    echo "      <input type=\"submit\" value=\"Modifier\">\n";
    echo "	 </form>\n";
    
    echo "	 <form action=\"index.php?page=clubs\" method=\"POST\" onsubmit=\"return(confirm('Supprimer ".$donnees['nom']." ?'))\">\n";
    echo "      <input type=\"hidden\" name=\"supprime\" value=1>\n";
    echo "      <input type=\"hidden\" name=\"id_club\" value=$idClub>\n";
    echo "      <input type=\"hidden\" name=\"nom\" value=\"".$donnees['nom']."\">\n";
    echo "      <input type=\"submit\" value=\"&#9888 Supprimer ".$donnees['nom']." &#9888\">\n"; // Bouton Supprimer
    echo "	 </form>\n";
    $reponse->closeCursor(); // Termine le traitement de la requête   
    }
}
?>
    </section>
    
