<?php session_start();?>
<?php include("inc_header.php");?>

<?php include("inc_connexion.php");?>

<?php include("inc_classes.php");?>

<?php include("inc_fonctions.php");?>

<?php

$page="";
if(isset($_GET['page'])) $page=$_GET['page'];

if($page=="home") $_SESSION = array();

if(empty($_SESSION['idSaison'])) include("home.php");
elseif(empty($_SESSION['idChampionnat'])) include("home.php");

// Si saison et championnat choisis alors on affiche la page
if(($_SESSION['idSaison']>0)&&($_SESSION['idChampionnat']>0)){


    if($page!="") include($page.".php");
    else {
    echo "<nav>\n";
    echo "  <a href=\"index.php?page=home\">".$_SESSION['nomSaison']." &#10060;</a>\n";
    echo "  <a href=\"index.php?page=championnats&sortie=1\">".$_SESSION['nomChampionnat']." &#10060;</a>\n";
    if(isset($_SESSION['idJournee'])) {
        echo "  	<a href=\"index.php?page=journees&sortie=1\">J".$_SESSION['numJournee']." &#10060;</a>\n"; // Sortir
    }
    echo "	<a href=\"index.php?page=saisons\">&#127937; Saisons</a>\n"; // Saisons
    echo "	<a href=\"index.php?page=tableau\">&#127942; Championnats</a>\n"; // Championnats
    echo "	<a href=\"index.php?page=journees\">&#128198; Journées</a>\n"; // Journées
    echo "	<a href=\"index.php?page=valeurs\">&#9876; Clubs</a>\n"; // Clubs
    echo "	<a href=\"index.php?page=joueurs\">&#127939; Joueurs</a>\n"; // Joueurs
    echo "</nav>\n";
    }
}

// Choix de la saison
if(isset($_POST['choixSaison'])) {
    $v=explode(",",$_POST['choixSaison']);
    $_SESSION['idSaison']=$v[0];
    $_SESSION['nomSaison']=$v[1];
    echo "<section>\n";
    popup("Saison : ".$_SESSION['nomSaison'].".","/");
    echo "</section>\n";
}
if(isset($_POST['choixChampionnat'])) {
    $v=explode(",",$_POST['choixChampionnat']);
    $_SESSION['idChampionnat']=$v[0];
    $_SESSION['nomChampionnat']=$v[1];
    echo "<section>\n";
    popup("Championnat : ".$_SESSION['nomChampionnat'].".","/");
    echo "</section>\n";
}
if(isset($_POST['choixJournee'])&&(!isset($_SESSION['idJournee']))) {
    $v=explode(",",$_POST['choixJournee']);
    $_SESSION['idJournee']=$v[0];
    $_SESSION['numJournee']=$v[1];
    echo "<section>\n";
    popup("Journée : J".$_SESSION['numJournee'].".","/");
    echo "</section>\n";
}
if(($_SESSION['idSaison']>0)&&($_SESSION['idChampionnat']>0)){
    if($page==""){
    echo "<section>\n";
        
    echo "        <ul class=\"menu\">\n";
    echo "            <li><h2>";
    echo $_SESSION['nomChampionnat'];
    echo "</h2>\n                <ul>\n";
    echo "                    <li><a href=\"index.php?page=tableau\">Tableau de bord<br /><big>&#127942;</big></a></li>\n";
    echo "                    <li><a href=\"index.php?page=championnats\">Classement<br /><big>&#127942;</big></a></li>\n";
    echo "               </ul>\n";
    echo "            </li>\n";
    if(isset($_SESSION['idJournee'])){
    echo "            <li><h2>Journée ".$_SESSION['numJournee']."</h2>\n";
    echo "                <ul>\n";
    echo "                    <li><a href=\"index.php?page=journees\">Statistiques<br /><big>&#128198;</big></a></li>\n";
    echo "                    <li><a href=\"index.php?page=equipe_type\">&Eacute;quipe type<br /><big>&#128198;</big></a></li>\n";
    echo "                    <li><a href=\"index.php?page=pronos\">Pronostics<br /><big>&#128198;</big></a></li>\n";
    echo "                    <li><a href=\"index.php?page=resultats\">Résultats<br /><big>&#128198;</big></a></li>\n";
    echo "                </ul>\n";
    echo "            </li>\n";
} else {
    echo "            <li><h2>Journée</h2>\n";
    echo "                <ul>\n";
    echo "   <form action=\"index.php\" method=\"POST\">\n";             // Modifier
    echo "    <label>Choisir la journée :</label>\n";        
    include("journees_select.php");
    echo "      <noscript><input type=\"submit\"></noscript>\n";
    echo "	 </form>\n";
    
        $reponse = $bdd->query("SELECT DISTINCT j.numero FROM journees j 
        LEFT JOIN matchs m ON m.id_journee=j.id_journee 
        WHERE m.resultat='' AND j.id_saison=".$_SESSION['idSaison']." AND j.id_championnat=".$_SESSION['idChampionnat']." ORDER BY j.numero;"); 
        $donnees = $reponse->fetch();
        echo "  	<form action=\"index.php\" method=\"POST\">";
        echo "<input type=\"hidden\" name=\"choixJournee\" value=\"".$donnees['num_journee'].",".$donnees['numero']."\">";
        echo "<input type=\"submit\" value=\"J".$donnees['numero']."\">";
        echo "</form>\n";
    
    echo "                </ul>\n";
    echo "            </li>\n";
}
    echo "            <li><h2>Clubs</h2>\n";
    echo "                <ul>\n";
    echo "                    <li><a href=\"index.php?page=valeurs\">Valeurs marchandes<br /><big>&#9876;</big></a></li>\n";
    echo "                </ul>\n";
    echo "            </li>\n";
    echo "            <li><h2>Joueurs</h2>\n";
    echo "                <ul>\n";
    echo "                    <li><a href=\"index.php?page=joueurs\">Meilleurs joueurs<br /><big>&#127939;</big></a></li>\n";
    echo "                </ul>\n";
    echo "            </li>\n";
    echo "        </ul>\n";
    echo "    </section>\n";

    }
    
}
?>


<?php include("inc_footer.php");?>
