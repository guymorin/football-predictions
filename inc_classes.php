<?php
function valColor($val){
    switch($val){
        case $val>0:
                $color="green";
            break;
        case $val<0:
                $color="red";
            break;
        default:
            $color="black";
    }
    return $color;
}
function criteres($type,$donnees,$bdd){
    $v=0;
    switch($type){
        case "motivC1":
                if($donnees['motivation_confiance1']!="") $v=$donnees['motivation_confiance1'];
                else $v=1; // Avantage à domicile
            break;
        case "motivC2":
                if($donnees['motivation_confiance2']!="") $v=$donnees['motivation_confiance2'];
            break;
        case "serieC1":
                if($donnees['serie_en_cours1']!="") $v=$donnees['serie_en_cours1'];
                elseif(($_SESSION['numJournee']-1)>0) {
                    $num = ($_SESSION['numJournee']-1);
                    $req="
                    SELECT m.equipe_1 as club FROM matchs m
                    LEFT JOIN saison_championnat_club s ON s.id_club=m.equipe_1
                    LEFT JOIN journees j ON j.id_journee=m.id_journee
                    WHERE j.numero='".$num."'
                    AND m.equipe_1='".$donnees['eq1']."' 
                    AND m.resultat='1'
                    AND s.id_championnat='".$_SESSION['idChampionnat']."'
                    AND s.id_saison='".$_SESSION['idSaison']."'
                    UNION
                    SELECT m.equipe_2 as club FROM matchs m
                    LEFT JOIN saison_championnat_club s ON s.id_club=m.equipe_2
                    LEFT JOIN journees j ON j.id_journee=m.id_journee
                    WHERE j.numero='".$num."'
                    AND m.equipe_2='".$donnees['eq1']."' 
                    AND m.resultat='2'
                    AND s.id_championnat='".$_SESSION['idChampionnat']."'
                    AND s.id_saison='".$_SESSION['idSaison']."'";
                    $r = $bdd->query($req);
                    while($data=$r->fetchColumn(0))   $res[] = $data;
                    if(in_array($donnees['eq1'],$res)) $v=1;
                }
            break;
        case "serieC2":
                if($donnees['serie_en_cours2']!="") $v=$donnees['serie_en_cours2'];
                elseif(($_SESSION['numJournee']-1)>0) {
                    $num = ($_SESSION['numJournee']-1);
                    $req="
                    SELECT m.equipe_1 as club FROM matchs m
                    LEFT JOIN saison_championnat_club s ON s.id_club=m.equipe_1
                    LEFT JOIN journees j ON j.id_journee=m.id_journee
                    WHERE j.numero='".$num."'
                    AND m.equipe_1='".$donnees['eq2']."' 
                    AND m.resultat='1'
                    AND s.id_championnat='".$_SESSION['idChampionnat']."'
                    AND s.id_saison='".$_SESSION['idSaison']."'
                    UNION
                    SELECT m.equipe_2 as club FROM matchs m
                    LEFT JOIN saison_championnat_club s ON s.id_club=m.equipe_2
                    LEFT JOIN journees j ON j.id_journee=m.id_journee
                    WHERE j.numero='".$num."'
                    AND m.equipe_2='".$donnees['eq2']."' 
                    AND m.resultat='2'
                    AND s.id_championnat='".$_SESSION['idChampionnat']."'
                    AND s.id_saison='".$_SESSION['idSaison']."'";
                    $r = $bdd->query($req);
                    while($data=$r->fetchColumn(0))   $res[] = $data;
                    if(in_array($donnees['eq2'],$res)) $v=1;
                }
            break;
        case "v1":
                $req="SELECT valeur FROM valeurs WHERE id_club='".$donnees['eq1']."' AND id_saison='".$_SESSION['idSaison']."';";
                $r = $bdd->query($req)->fetch();
                $v = $r[0]; 
            break;
        case "v2":
                $req="SELECT valeur FROM valeurs WHERE id_club='".$donnees['eq2']."' AND id_saison='".$_SESSION['idSaison']."';";
                $r = $bdd->query($req)->fetch();
                $v = $r[0];
            break;
        case "msDom":
                if(isset($donnees['Dom'])) $v=$donnees['Dom'];
            break;
        case "msNul":
                if(isset($donnees['Nul'])) $v=$donnees['Nul'];
            break;
        case "msExt":
                if(isset($donnees['Ext'])) $v=$donnees['Ext'];
            break;
    }
    return $v;
}

function popup($texte,$lien){

    echo "  <div id=\"overlay\"><div class=\"update\"><a class=\"close\" href=\"".$lien."\">&times;</a><p>".$texte."</p><p><a href=\"".$lien."\">Ok</a></p></div></div>\n";

}
function changeJ($bdd,$titre,$page){
    // Flèche changement de journée
    echo "<div id=\"changeJ\">\n";
        $req="SELECT id_journee, numero FROM journees WHERE numero>=".($_SESSION['numJournee']-1)." AND numero<=".($_SESSION['numJournee']+1)." AND id_saison='".$_SESSION['idSaison']."' AND id_championnat='".$_SESSION['idChampionnat']."' ORDER BY numero;";
        
        $reponse = $bdd->query($req);
        $nb=sizeof($reponse->fetchAll());
        if($nb==2) $ajout="<input type=\"submit\" value=\"\" readonly>\n";
        $cpt=0;
        
        $reponse = $bdd->query($req);
        while ($donnees = $reponse->fetch())
        {
            $cpt++;
            
            if($donnees['numero']==$_SESSION['numJournee']-1) $cpt="&#x3008;";//précédente
            if($donnees['numero']==$_SESSION['numJournee']+1) $cpt="&#x3009;";//suivante
            
            if($donnees['numero']==$_SESSION['numJournee']) {
                if($cpt==1) echo $ajout;
                echo "<h2>".$titre." J".$_SESSION['numJournee']."</h2>\n";
                if($nb==1)   echo $ajout;
            } else {
                echo "<form action=\"index.php?page=".$page."\" method=\"POST\">\n";
                echo "  <input type=\"hidden\" name=\"choixJournee\" value=\"".$donnees['id_journee'].",".$donnees['numero']."\">\n";
                echo "  <input type=\"submit\" value=\"".$cpt."\">\n";
                echo "</form>\n";
            }
            $nb--;
        }
        
    echo "</div>\n";
    $reponse->closeCursor(); // Termine le traitement de la requête
}
?>
