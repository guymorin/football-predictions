<?php
// NAVIGATION CLUB VALEUR
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
$valClub=0;
if(isset($_POST['id_club'])) $idClub=$_POST['id_club'];
if(isset($_POST['valeur'])) $valClub=$_POST['valeur'];
$val=array_combine($idClub,$valClub);
$modifie=1;
if(isset($_POST['modifie'])) $modifie=$_POST['modifie'];

// MODIFIER
if($modifie==1){

echo "<h2>Valeurs</h2>\n";

    // S'il y a une modification
    if(isset($val)) {
        $bdd->exec("ALTER TABLE valeurs AUTO_INCREMENT=0;");
        $req="";
        foreach($val as $k=>$v){
            if($v>0){
            
                $reponse = $bdd->query("SELECT COUNT(*) as nb FROM valeurs WHERE id_saison='".$_SESSION['idSaison']."' AND id_club='".$k."';");
                $donnees = $reponse->fetch();
                $reponse->closeCursor(); // Termine le traitement de la requête
                echo $donnees[0];
                
                if($donnees[0]==0) {
                    $req.="INSERT INTO valeurs VALUES(NULL,'".$_SESSION['idSaison']."','".$k."','".$v."');";
                }
                if($donnees[0]==1) {
                    $req.="UPDATE valeurs SET valeur='".$v."' WHERE id_saison='".$_SESSION['idSaison']."' AND id_club='".$k."';";
                }
            
            }
        } 
        $bdd->exec($req);
        popup("Modification des valeurs.","index.php?page=valeurs");

    } else {
    
    // Affichage des clubs
    $req = "SELECT c.*, v.valeur 
    FROM clubs c 
    LEFT JOIN valeurs v ON v.id_club=c.id_club
    LEFT JOIN saison_championnat_club scc ON scc.id_club=c.id_club 
    WHERE scc.id_saison='".$_SESSION['idSaison']."' 
    AND scc. id_championnat='".$_SESSION['idChampionnat']."';";
    $reponse = $bdd->query($req);
    echo "	 <form action=\"index.php?page=valeurs\" method=\"POST\">\n";
    echo "    <label>Modifier les valeurs :</label>\n";  
    echo "  <table>\n";
    echo "      <tr><th>Club</th><th>Valeur (M €)</th></tr>\n";
        // On affiche chaque entrée
        while ($donnees = $reponse->fetch())
        {
            echo "  		<tr><td><input type=\"hidden\" name=\"id_club[]\" readonly  value=\"".$donnees['id_club']."\">".$donnees['nom']."</td><td><input type=\"text\" name=\"valeur[]\" value=\"".$donnees['valeur']."\"></td></tr>\n";
        }
    echo "  </table>\n";
    echo "      <input type=\"submit\">\n";
    
    $reponse->closeCursor(); // Termine le traitement de la requête   
    }
}
?>
    </section>
    
