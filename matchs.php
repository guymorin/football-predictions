<?php
// NAVIGATION JOURNEES
include("journees_nav.php");
?>

    <section>
<?php

if(empty($_SESSION['idJournee'])) {
    if(isset($_POST['choixJournee'])){

        $v=explode(",",$_POST['choixJournee']);
        $_SESSION['idJournee']=$v[0];
        $_SESSION['numJournee']=$v[1];

        popup("Journée : J".$_SESSION['numJournee'].".","index.php?page=matchs");

    
    } else {
        echo "   <form action=\"index.php?page=matchs\" method=\"POST\">\n";             // Modifier
        echo "    <label>Matchs de la journée :</label>\n";        
        include("journees_select.php");
        echo "      <noscript><input type=\"submit\"></noscript>\n";
        echo "	 </form>\n";
    }
} else {

$idMatch=$eq1=$eq2=0;
$resultat=$date="";
$cote1=$coteN=$cote2=0;
if(isset($_POST['id_match'])) $idMatch=$_POST['id_match'];
if(isset($_POST['equipe_1'])) $eq1=$_POST['equipe_1'];
if(isset($_POST['equipe_2'])) $eq2=$_POST['equipe_2'];
if(isset($_POST['resultat'])) $resultat=$_POST['resultat'];
if(isset($_POST['cote1'])) $cote1=$_POST['cote1'];
if(isset($_POST['coteN'])) $coteN=$_POST['coteN'];
if(isset($_POST['cote2'])) $cote2=$_POST['cote2'];
if(isset($_POST['date'])) $date=$_POST['date'];

$cree=0;
$modifie=0;
$supprime=0;
$sortie=0;
if(isset($_GET['cree'])) $cree=$_GET['cree'];
if(isset($_POST['cree'])) $cree=$_POST['cree'];
if(isset($_GET['modifie'])) $modifie=$_GET['modifie'];
if(isset($_POST['modifie'])) $modifie=$_POST['modifie'];
if(isset($_POST['supprime'])) $supprime=$_POST['supprime'];
if(isset($_GET['sortie'])) $sortie=$_GET['sortie'];

// SORTIR DE LA JOURNEE
if($sortie==1){
    unset($_SESSION['idJournee']);
    unset($_SESSION['numJournee']);
    popup("Sortie de la journée.","index.php?page=journees");
}

// SUPPRIMER
if($supprime==1){
        $req="DELETE FROM matchs WHERE id_match='".$idMatch."';";
        $bdd->exec($req);
        $bdd->exec("ALTER TABLE matchs AUTO_INCREMENT=0;");
        popup("Suppression pour le match ".$idMatch.".","index.php?page=matchs");
}

// CREER
elseif($cree==1){

    echo "<h2>Créer un match J".$_SESSION['numJournee']."</h2>\n";

    // S'il y a une création
    if(($eq1>0)&&($eq2>0)&&($eq1!=$eq2)) {
        $bdd->exec("ALTER TABLE matchs AUTO_INCREMENT=0;");
        $req="INSERT INTO matchs VALUES(NULL,'".$_SESSION['idJournee']."','".$eq1."','".$eq2."','".$resultat."','".$cote1."','".$coteN."','".$cote2."','".$date."',0,0,0,0);";
        $bdd->exec($req);
        popup("Création du match.","index.php?page=matchs&cree=1");
    } else {
          
        echo "	    <form action=\"index.php?page=matchs\" method=\"POST\">\n";
        echo "      <input type=\"hidden\" name=\"cree\" value=\"1\">\n"; 
        
        echo "	    <p><label>Id. journée</label>\n";
        echo "      <input type=\"text\" readonly name=\"idJournee\" value=\"".$_SESSION['idJournee']."\"></p>\n"; 
        
        $req="SELECT c.id_club, c.nom FROM clubs c LEFT JOIN saison_championnat_club scc ON c.id_club=scc.id_club WHERE scc.id_saison='".$_SESSION['idSaison']."' AND scc.id_championnat='".$_SESSION['idChampionnat']."' ORDER BY c.nom;";
        $reponse = $bdd->query($req);
        
        echo "	    <label>&Eacute;quipe 1</label>\n";
        echo "  	<select name=\"equipe_1\">\n";
        include("clubs_select.php");
        echo "  	</select>\n";
        echo "	    <label>&Eacute;quipe 2</label>\n";
        $reponse = $bdd->query($req);
        echo "  	<select name=\"equipe_2\">\n";
        include("clubs_select.php");
        echo "  	</select>\n";
        
        $reponse->closeCursor(); // Termine le traitement de la requête
        
        echo "	    <p><label>Date :</label>\n";
        echo "         <input type=\"date\" name=\"date\" value=\"\">\n";
        echo "      </p>\n";
        echo "	    <p><label>Cotes :</label>\n";
        echo "         1<input type=\"number\" step=\"0.01\" size=\"2\" name=\"cote1\" value=\"0\">\n";
        echo "         N<input type=\"number\" step=\"0.01\" size=\"2\" name=\"coteN\" value=\"0\">\n";
        echo "         2<input type=\"number\" step=\"0.01\" size=\"2\" name=\"cote2\" value=\"0\">\n";
        echo "      </p>\n";
        
        echo "	    <p><label>Résultat :</label>\n";
        echo "     <input type=\"radio\" name=\"resultat\" id=\"1\" value=\"1\"";
        echo "><label for=\"1\">Domicile</label>\n";
        echo "     <input type=\"radio\" name=\"resultat\" id=\"N\" value=\"N\"";
        echo "><label for=\"N\">Nul</label>\n";
        echo "     <input type=\"radio\" name=\"resultat\" id=\"2\" value=\"2\"";
        echo "><label for=\"2\">Extérieur</label>\n";
        
        echo "     <input type=\"submit\" value=\"Créer\">\n";

        echo "	    </form>\n";   

	}
	
}
// MODIFIER
else{

    echo "<h2>Modifier un match J".$_SESSION['numJournee']."</h2>\n";

    // S'il y a une modification
    if(($eq1>0)&&($eq2>0)&&($eq1!=$eq2)) {
        $req="UPDATE matchs SET id_journee='".$_SESSION['idJournee']."', equipe_1='".$eq1."', equipe_2='".$eq2."', resultat='".$resultat."' WHERE id_match='".$idMatch."';";
        $bdd->exec($req);
        popup("Modification du match.","index.php?page=matchs");
    } elseif($idMatch>0) {
    
    // Si un match est sélectionné alors affichage
        $req="SELECT m.id_match,c1.nom as nom1,c2.nom as nom2,c1.id_club as id1,c2.id_club as id2, m.resultat, m.date, m.cote1, m.coteN, m.cote2 FROM matchs m LEFT JOIN clubs c1 ON m.equipe_1=c1.id_club LEFT JOIN clubs c2 ON m.equipe_2=c2.id_club WHERE m.id_match='".$idMatch."';";
        $reponse = $bdd->query($req);
        $donnees = $reponse->fetch();
        $nom1=$donnees['nom1'];
        $nom2=$donnees['nom2'];
        $id1=$donnees['id1'];
        $id2=$donnees['id2'];
        $resultat=$donnees['resultat'];
        $date=$donnees['date'];
        $cote1=$donnees['cote1'];
        $coteN=$donnees['coteN'];
        $cote2=$donnees['cote2'];
        
        echo "	 <form action=\"index.php?page=matchs\" method=\"POST\">\n";
        echo "      <input type=\"hidden\" name=\"modifie\" value=1>\n";    
        
        echo "	 <p><label>Id.</label>\n";
        echo "      <input type=\"text\" name=\"id_match\" readonly value=\"".$donnees['id_match']."\"></p>\n";

        echo "	 <label>&Eacute;quipe 1</label>\n";
        echo "  	<select name=\"equipe_1\">\n";
        echo "  		<option value=\"0\">...</option>\n";
        $reponse = $bdd->query("SELECT c.* FROM clubs c LEFT JOIN saison_championnat_club scc ON c.id_club=scc.id_club WHERE scc.id_saison='".$_SESSION['idSaison']."' AND scc.id_championnat='".$_SESSION['idChampionnat']."' ORDER BY nom;");
        // On affiche chaque entrée
        while ($donnees = $reponse->fetch())
        {
            echo "  		<option value=\"".$donnees['id_club']."\"";
            if($donnees['id_club']==$id1) echo " selected";
            echo ">".$donnees['nom']."</option>\n";
        }
        echo "  	</select>\n";
        
        
        echo "	 <label>&Eacute;quipe 2</label>\n";
        echo "  	<select name=\"equipe_2\">\n";
        $reponse = $bdd->query("SELECT c.* FROM clubs c LEFT JOIN saison_championnat_club scc ON c.id_club=scc.id_club WHERE scc.id_saison='".$_SESSION['idSaison']."' AND scc.id_championnat='".$_SESSION['idChampionnat']."' ORDER BY nom;");      
        echo "  		<option value=\"0\">...</option>\n";
        // On affiche chaque entrée
        while ($donnees = $reponse->fetch())
        {
             echo "  		<option value=\"".$donnees['id_club']."\"";
            if($donnees['id_club']==$id2) echo " selected";
            echo ">".$donnees['nom']."</option>\n";        }
        echo "  	</select>\n";

        echo "	    <p><label>Date :</label>\n";
        echo "         <input type=\"date\" name=\"date\" value=\"".$date."\">\n";
        echo "      </p>\n";
        echo "	    <p><label>Cotes :</label>\n";
        echo "         1<input type=\"number\" step=\"0.01\" size=\"2\" name=\"cote1\" value=\"".$cote1."\">\n";
        echo "         N<input type=\"number\" step=\"0.01\" size=\"2\" name=\"coteN\" value=\"".$coteN."\">\n";
        echo "         2<input type=\"number\" step=\"0.01\" size=\"2\" name=\"cote2\" value=\"".$cote2."\">\n";
        echo "      </p>\n";
        
        echo "	    <p><label>Résultat :</label>\n";
        echo "     <input type=\"radio\" name=\"resultat\" id=\"1\" value=\"1\"";
        if($resultat=="1") echo " checked";
        echo "><label for=\"1\">Domicile</label>\n";
        echo "     <input type=\"radio\" name=\"resultat\" id=\"N\" value=\"N\"";
        if($resultat=="N") echo " checked";
        echo "><label for=\"N\">Nul</label>\n";
        echo "     <input type=\"radio\" name=\"resultat\" id=\"2\" value=\"2\"";
        if($resultat=="2") echo " checked";
        echo "><label for=\"2\">Extérieur</label>\n";
        
        echo "      <input type=\"submit\" value=\"Modifier\">\n";
        echo "	 </form>\n";
        
        echo "	 <form action=\"index.php?page=matchs\" method=\"POST\" onsubmit=\"return(confirm('Supprimer ".$nom1." - ".$nom2." ?'))\">\n";
        echo "      <input type=\"hidden\" name=\"supprime\" value=1>\n";
        echo "      <input type=\"hidden\" name=\"id_match\" value=$idMatch>\n";
        echo "      <input type=\"submit\" value=\"&#9888 Supprimer ".$nom1." - ".$nom2." &#9888\">\n"; // Bouton Supprimer
        echo "	 </form>\n";
        $reponse->closeCursor(); // Termine le traitement de la requête   
    
    } else {
    
        // Sinon affichage de tous les matchs
        echo "  	<form action=\"index.php?page=matchs\" method=\"POST\">\n";             // Modifier
        echo "      <input type=\"hidden\" name=\"modifie\" value=\"1\">\n"; 
        echo "    <label>Modifier un match :</label>\n";                                    
        echo "  	<select multiple size=\"10\" name=\"id_match\">\n";
        // On affiche chaque entrée
        $reponse = $bdd->query("SELECT m.id_match,c1.nom as nom1,c2.nom as nom2, m.resultat FROM matchs m LEFT JOIN clubs c1 ON m.equipe_1=c1.id_club LEFT JOIN clubs c2 ON m.equipe_2=c2.id_club WHERE m.id_journee='".$_SESSION['idJournee']."';");
        while ($donnees = $reponse->fetch())
        {
            echo "  		<option value=\"".$donnees['id_match']."\">";
            if($donnees['resultat']!="") echo "[".$donnees['resultat']."] ";
            echo $donnees['nom1']." - ".$donnees['nom2']."</option>\n";
        }
        echo "	 </select>\n";
        echo "      <input type=\"submit\">\n";
        echo "	 </form>\n";
        $reponse->closeCursor(); // Termine le traitement de la requête   
    }
}

}
?>
    </section>
    
