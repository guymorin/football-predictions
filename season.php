<?php
// NAVIGATION SAISON
echo "  <nav>\n";
$reponse = $bdd->query("SELECT * FROM saisons ORDER BY nom;");
echo "  	<a href=\"/\">&#8617</a>\n";                                   // Retour
echo "  	<a href=\"index.php?page=saisons&cree=1\">Créer une saison</a>\n"; // Créer

echo "  	<form action=\"index.php?page=saisons\" method=\"POST\">\n";             // Modifier
echo "      <input type=\"hidden\" name=\"modifie\" value=\"1\">\n"; 
echo "    <label>Modifier une saison :</label>\n";                                    
echo "  	<select name=\"id_saison\" onchange=\"submit()\">\n";
echo "  		<option value=\"0\">...</option>\n";
// On affiche chaque entrée
while ($donnees = $reponse->fetch())
{
    echo "  		<option value=\"".$donnees['id_saison']."\"";
    if($donnees['id_saison']==$_SESSION['idSaison']) echo " disabled";
    echo ">".$donnees['nom']."</option>\n";
}
echo "	 </select>\n";
echo "      <noscript><input type=\"submit\"></noscript>\n";
echo "	 </form>\n";
echo "      </nav>\n";
$reponse->closeCursor(); // Termine le traitement de la requête

?>

    <section>
<?php
$idSaison=0;
$nomSaison="";
if(isset($_POST['id_saison'])) $idSaison=$_POST['id_saison'];
if(isset($_POST['nom'])) $nomSaison=$_POST['nom'];

$cree=0;
$modifie=0;
$supprime=0;
if(isset($_GET['cree'])) $cree=$_GET['cree'];
if(isset($_POST['cree'])) $cree=$_POST['cree'];
if(isset($_POST['modifie'])) $modifie=$_POST['modifie'];
if(isset($_POST['supprime'])) $supprime=$_POST['supprime'];

// SUPPRIMER
if($supprime==1){
        $req="DELETE FROM saisons WHERE id_saison='".$idSaison."';";
        $bdd->exec($req);
        $bdd->exec("ALTER TABLE saisons AUTO_INCREMENT=0;");
        popup("Suppression de la saison.","index.php?page=saisons");
}

// CREER
if($cree==1){

    echo "<h2>Créer une saison</h2>\n";

    // S'il y a une création
    if($nomSaison!="") {
        $bdd->exec("ALTER TABLE saisons AUTO_INCREMENT=0;");
        $req="INSERT INTO saisons VALUES(NULL,'".$nomSaison."');";
        $bdd->exec($req);
        popup("Création de la saison.","index.php?page=saisons");
    } else {
    
	echo "	    <form action=\"index.php?page=saisons\" method=\"POST\">\n";
    echo "      <input type=\"hidden\" name=\"cree\" value=\"1\">\n"; 
	echo "	    <label>Nom</label>\n";
	echo "     <input type=\"text\" name=\"nom\" value=\"".$nomSaison."\">\n";
	echo "     <input type=\"submit\" value=\"Créer\">\n";

	echo "	    </form>\n";   

	}
	
}
// MODIFIER
elseif($modifie==1){

    echo "<h2>Modifier une saison</h2>\n";

    // S'il y a une modification
    if($nomSaison!="") {
        $req="UPDATE saisons SET nom='".$nomSaison."' WHERE id_saison='".$idSaison."';";
        $bdd->exec($req);
        popup("Modification de la saison.","index.php?page=saisons");
    } else {
    
    // Affichage de la saison sélectionnée

    $reponse = $bdd->query("SELECT * FROM saisons WHERE id_saison='".$idSaison."';");
    echo "	 <form action=\"index.php?page=saisons\" method=\"POST\">\n";
    $donnees = $reponse->fetch();

    echo "      <input type=\"hidden\" name=\"modifie\" value=1>\n";    
    
    echo "	 <label>Id.</label>\n";
    echo "      <input type=\"text\" name=\"id_saison\" readonly=\"readonly\" value=\"".$donnees['id_saison']."\">\n";

    echo "	 <label>Nom</label>\n";
    echo "      <input type=\"text\" name=\"nom\" value=\"".$donnees['nom']."\">\n";
    echo "      <input type=\"submit\" value=\"Modifier\">\n";
    echo "	 </form>\n";
    
    echo "	 <form action=\"index.php?page=saisons\" method=\"POST\" onsubmit=\"return(confirm('Supprimer ".$donnees['nom']." ?'))\">\n";
    echo "      <input type=\"hidden\" name=\"supprime\" value=1>\n";
    echo "      <input type=\"hidden\" name=\"id_saison\" value=$idSaison>\n";
    echo "      <input type=\"hidden\" name=\"nom\" value=\"".$donnees['nom']."\">\n";
    echo "      <input type=\"submit\" value=\"&#9888 Supprimer ".$donnees['nom']." &#9888\">\n"; // Bouton Supprimer
    echo "	 </form>\n";
    $reponse->closeCursor(); // Termine le traitement de la requête   
    }
}
else {
    echo "<h2>Saisons</h2>\n";
    echo "<h3>".$_SESSION['nomSaison']."</h3>\n";
}
?>
    </section>
    
