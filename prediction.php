<?php
include("inc_changeJ.php");
// NAVIGATION MATCHS PRONOS
include("journees_nav.php");
?>

    <section>
<?php
$modifie=0;
if(isset($_POST['modifie'])) $modifie=$_POST['modifie'];
$expert=0;
if(isset($_POST['expert'])) $expert=$_POST['expert'];
if(isset($_GET['expert'])) $expert=$_GET['expert'];
// MODIFIER
if($modifie==1){
    // S'il y a une modification
    
$rMatch="";
if(isset($_POST['id_match'])) $idMatch=$_POST['id_match'];
if(isset($_POST['resultat'])) $rMatch=$_POST['resultat'];


if(isset($_POST['motivation_confiance1'])) $moMatch1=array_filter($_POST['motivation_confiance1']);
if(isset($_POST['serie_en_cours1'])) $seMatch1=array_filter($_POST['serie_en_cours1']);
if(isset($_POST['forme_physique1'])) $foMatch1=array_filter($_POST['forme_physique1']);
if(isset($_POST['meteo1'])) $meMatch1=array_filter($_POST['meteo1']);
if(isset($_POST['joueurs_cles1'])) $joMatch1=array_filter($_POST['joueurs_cles1']);
if(isset($_POST['valeur_marchande1'])) $vaMatch1=array_filter($_POST['valeur_marchande1']);
if(isset($_POST['domicile_exterieur1'])) $doMatch1=array_filter($_POST['domicile_exterieur1']);
if(isset($_POST['motivation_confiance2'])) $moMatch2=array_filter($_POST['motivation_confiance2']);
if(isset($_POST['serie_en_cours2'])) $seMatch2=array_filter($_POST['serie_en_cours2']);
if(isset($_POST['forme_physique2'])) $foMatch2=array_filter($_POST['forme_physique2']);
if(isset($_POST['meteo2'])) $meMatch2=array_filter($_POST['meteo2']);
if(isset($_POST['joueurs_cles2'])) $joMatch2=array_filter($_POST['joueurs_cles2']);
if(isset($_POST['valeur_marchande2'])) $vaMatch2=array_filter($_POST['valeur_marchande2']);
if(isset($_POST['domicile_exterieur2'])) $doMatch2=array_filter($_POST['domicile_exterieur2']);

        $bdd->exec("ALTER TABLE criteres AUTO_INCREMENT=0;");
        $req="";
        
        foreach($idMatch as $k){
            if($rMatch[$k]!=""){// Si resultat
                $req.="UPDATE matchs SET resultat='".$rMatch[$k]."' WHERE id_match='".$k."';";
            }

            $reponse = $bdd->query("SELECT COUNT(*) as nb FROM criteres WHERE id_match='".$k."';");
            $donnees = $reponse->fetch();
            $reponse->closeCursor(); // Termine le traitement de la requête
            if($donnees[0]==0) {
                $req.="INSERT INTO criteres VALUES(NULL,'".$k."','";
                if(isset($moMatch1[$k])) $req.=$moMatch1[$k];else $req.=0;
                $req.="','";
                if(isset($seMatch1[$k])) $req.=$seMatch1[$k];else $req.=0;
                $req.="','";
                if(isset($foMatch1[$k])) $req.=$foMatch1[$k];else $req.=0;
                $req.="','";
                if(isset($meMatch1[$k])) $req.=$meMatch1[$k];else $req.=0;
                $req.="','";
                if(isset($joMatch1[$k])) $req.=$joMatch1[$k];else $req.=0;
                $req.="','";
                if(isset($vaMatch1[$k])) $req.=$vaMatch1[$k];else $req.=0;
                $req.="','";
                if(isset($doMatch1[$k])) $req.=$doMatch1[$k];else $req.=0;
                $req.="','";
                if(isset($moMatch2[$k])) $req.=$moMatch2[$k];else $req.=0;
                $req.="','";
                if(isset($seMatch2[$k])) $req.=$seMatch2[$k];else $req.=0;
                $req.="','";
                if(isset($foMatch2[$k])) $req.=$foMatch2[$k];else $req.=0;
                $req.="','";
                if(isset($meMatch2[$k])) $req.=$meMatch2[$k];else $req.=0;
                $req.="','";
                if(isset($joMatch2[$k])) $req.=$joMatch2[$k];else $req.=0;
                $req.="','";
                if(isset($vaMatch2[$k])) $req.=$vaMatch2[$k];else $req.=0;
                $req.="','";
                if(isset($doMatch2[$k])) $req.=$doMatch2[$k];else $req.=0;
                $req.="');";
            }
            if($donnees[0]==1) {
                $req.="UPDATE criteres SET ";
                $req.="motivation_confiance1='";
                if(isset($moMatch1[$k])) $req.=$moMatch1[$k];else $req.=0;
                $req.="',";
                $req.="serie_en_cours1='";
                if(isset($seMatch1[$k])) $req.=$seMatch1[$k];else $req.=0;
                $req.="',";
                $req.="forme_physique1='";
                if(isset($foMatch1[$k])) $req.=$foMatch1[$k];else $req.=0;
                $req.="',";
                $req.="meteo1='";
                if(isset($meMatch1[$k])) $req.=$meMatch1[$k];else $req.=0;
                $req.="',";
                $req.="joueurs_cles1='";
                if(isset($joMatch1[$k])) $req.=$joMatch1[$k];else $req.=0;
                $req.="',";
                $req.="valeur_marchande1='";
                if(isset($vaMatch1[$k])) $req.=$vaMatch1[$k];else $req.=0;
                $req.="',";
                $req.="domicile_exterieur1='";
                if(isset($doMatch1[$k])) $req.=$doMatch1[$k];else $req.=0;
                $req.="',";
                $req.="motivation_confiance2='";
                if(isset($moMatch2[$k])) $req.=$moMatch2[$k];else $req.=0;
                $req.="',";
                $req.="serie_en_cours2='";
                if(isset($seMatch2[$k])) $req.=$seMatch2[$k];else $req.=0;
                $req.="',";
                $req.="forme_physique2='";
                if(isset($foMatch2[$k])) $req.=$foMatch2[$k];else $req.=0;
                $req.="',";
                $req.="meteo2='";
                if(isset($meMatch2[$k])) $req.=$meMatch2[$k];else $req.=0;
                $req.="',";
                $req.="joueurs_cles2='";
                if(isset($joMatch2[$k])) $req.=$joMatch2[$k];else $req.=0;
                $req.="',";
                $req.="valeur_marchande2='";
                if(isset($vaMatch2[$k])) $req.=$vaMatch2[$k];else $req.=0;
                $req.="',";
                $req.="domicile_exterieur2='";
                if(isset($doMatch2[$k])) $req.=$doMatch2[$k];else $req.=0;
                $req.="' WHERE id_match='".$k."';";
            }
            
        }
        
        $bdd->exec($req);
        popup("Modification des pronostics pour J".$_SESSION['numJournee'].".","index.php?page=pronos&expert=".$expert);

    } else {
    
    if($expert==1) include("pronos_matchs_expert.php");
    else include("pronos_matchs.php");
    
}
?>
    </section>
