<?php
include("champ_nav.php");
?>

    <section>
<?php

    echo "<h2>Championnats</h2>\n";
    echo "<h3>Tableau de bord</h3>\n";

    $graph=array(0=>0);
// STATISTIQUES
        $req="SELECT m.id_match,
        cr.motivation_confiance1,cr.motivation_confiance2,
        cr.serie_en_cours1,cr.serie_en_cours2,
        cr.forme_physique1,cr.forme_physique2,
        cr.meteo1,cr.meteo2,
        cr.joueurs_cles1,cr.joueurs_cles2,
        cr.valeur_marchande1,cr.valeur_marchande2,
        cr.domicile_exterieur1,cr.domicile_exterieur2,
        c1.nom as nom1,c2.nom as nom2,c1.id_club as eq1,c2.id_club as eq2,
        m.resultat, m.date, m.cote1, m.coteN, m.cote2, j.numero 
        FROM matchs m 
        LEFT JOIN clubs c1 ON m.equipe_1=c1.id_club 
        LEFT JOIN clubs c2 ON m.equipe_2=c2.id_club 
        LEFT JOIN criteres cr ON cr.id_match=m.id_match 
        LEFT JOIN journees j ON j.id_journee=m.id_journee 
        WHERE m.resultat<>'';
        ORDER BY j.numero 
        ;";
        $reponse = $bdd->query($req);

        $table="	 <table class=\"benef\">\n";
           
        $table.="  		<tr>\n";
        $table.="  		  <th>Journée</th>\n";
        $table.="  		  <th>Mises</th>\n";
        $table.="         <th>Succ&egrave;s</th>\n";
        $table.="         <th>Cote<br />moyenne<br />jouée</th>\n";
        $table.="         <th>Gains</th>\n";
        $table.="         <th>Bénéfice</th>\n";
        $table.="         <th>Bénéfice<br />total</th>\n";
        $table.="       </tr>\n";
        
    
        $jMisesTotal=$jSuccesTotal=$jGainsTotal;$jCoteJoueeTotal;$jBenefTotal=0;
        while ($donnees = $reponse->fetch())
        {
            $mCoteJouee=0;         

            // Calcul de la VM
            $vm1 = $donnees['valeur_marchande1']; 
            $vm2 = $donnees['valeur_marchande2']; 
            
            $dom = $donnees['domicile_exterieur1']; 
            $ext = $donnees['domicile_exterieur2']; 
            
            // Calcul des matchs similaires
                $req="SELECT SUM(CASE WHEN m.resultat = '1' THEN 1 ELSE 0 END) AS Dom,
                SUM(CASE WHEN m.resultat = 'N' THEN 1 ELSE 0 END) AS Nul,
                SUM(CASE WHEN m.resultat = '2' THEN 1 ELSE 0 END) AS Ext
                FROM matchs m 
                LEFT JOIN criteres cr ON cr.id_match=m.id_match 
                WHERE cr.motivation_confiance1='".$donnees['motivation_confiance1']."' 
                AND cr.motivation_confiance2='".$donnees['motivation_confiance2']."' 
                AND cr.serie_en_cours1='".$donnees['serie_en_cours1']."' 
                AND cr.serie_en_cours2='".$donnees['serie_en_cours2']."' 
                AND cr.forme_physique1='".$donnees['forme_physique1']."' 
                AND cr.forme_physique2='".$donnees['forme_physique2']."' 
                AND cr.meteo1='".$donnees['meteo1']."' 
                AND cr.meteo2='".$donnees['meteo2']."' 
                AND cr.joueurs_cles1='".$donnees['joueurs_cles1']."' 
                AND cr.joueurs_cles2='".$donnees['joueurs_cles2']."' 
                AND cr.valeur_marchande1='".$donnees['valeur_marchande1']."' 
                AND cr.valeur_marchande2='".$donnees['valeur_marchande2']."' 
                AND cr.domicile_exterieur1='".$donnees['domicile_exterieur1']."' 
                AND cr.domicile_exterieur2='".$donnees['domicile_exterieur2']."' 
                AND m.date<'".$donnees['date']."'";
                $r = $bdd->query($req)->fetch();
                $similairesDom=criteres("msDom",$r,$bdd);
                $similairesExt=criteres("msExt",$r,$bdd);
            
            // Calcul du total
            $gagne="";
            $id=$donnees['id_match'];
            $total1=
                $donnees['motivation_confiance1']
                +$donnees['serie_en_cours1']
                +$donnees['forme_physique1']
                +$donnees['meteo1']
                +$donnees['joueurs_cles1']
                +$similairesDom
                +$vm1
                +$dom;
            $total2=
                $donnees['motivation_confiance2']
                +$donnees['serie_en_cours2']
                +$donnees['forme_physique2']
                +$donnees['meteo2']
                +$donnees['joueurs_cles2']
                +$similairesExt
                +$vm2
                +$ext;
            if($total1>$total2) $prono="1";
            elseif($total1==$total2) $prono="N";
            elseif($total1<$total2) $prono="2";
            
            $coteJouee=0;
            switch($prono){
                case "1":
                    $mCoteJouee = $donnees['cote1'];
                    break;
                case "N":
                    $mCoteJouee = $donnees['coteN'];
                    break;
                case "2":
                    $mCoteJouee = $donnees['cote2'];
                    break;
            }
            
            if($prono==$donnees['resultat']){
                $jSucces++;
                $jGains+=$mCoteJouee;
            }
            $jCoteJouee+=$mCoteJouee;

            $jMatchs++;

            $jBenef=$jGains-$jMatchs;
            
            if($jMatchs==10){
                
                $jBenefTotal+=$jBenef;
                $jMisesTotal+=$jMatchs;
                $jSuccesTotal+=$jSucces;
                $jGainsTotal+=$jGains;
                $jCoteJoueeTotal+=$jCoteJouee;
                $table.="       <tr>\n";
                $table.="           <td><strong>".$donnees['numero']."</strong></td>\n";
                $table.="           <td>".$jMatchs."</td>\n";
                $table.="           <td>".$jSucces."</td>\n";
                $coteMoy=(round($jCoteJouee/$jMatchs,2));
                $table.="           <td>".$coteMoy."</td>\n";
                $table.="           <td>".(money_format('%i',$jGains))."</td>\n";
                $table.="           <td><span style=\"color:".valColor($jBenef)."\">";
                if($jBenef>0) $table.="+";
                $table.=(money_format('%i',$jBenef))."</span></td>\n";
                $table.="           <td><span style=\"color:".valColor($jBenefTotal)."\">";
                if($jBenefTotal>0) $table.="+";
                $table.=(money_format('%i',$jBenefTotal))."</span></td>\n";
                $table.="       </tr>\n";
                
                $jMatchs=$jSucces=$jCoteJouee=$jGains=$jBenef=0;
                $graph[$donnees['numero']]=$jBenefTotal;
            }
        }
        $reponse->closeCursor(); // Termine le traitement de la requête
        $table.="	 </table>\n";
        
        // Calculs
        $roi = round(($jBenefTotal/$jMisesTotal)*100);
        $tauxReussite = round(($jSuccesTotal/$jMisesTotal)*100);
        $gainParMise = (round($jGainsTotal/$jMisesTotal,2));
        
        echo "<p>\n<table class=\"stats\">\n";
        echo "  <tr>\n";
        echo "      <td>Mises</td>\n";
        echo "      <td>".$jMisesTotal."</td>\n";
        
        // Bénéfice
        echo "      <td>Bénéfice</td>\n";
        echo "      <td>";
        echo "<span style=\"color:".valColor($jBenefTotal)."\">";
        if($jBenefTotal>0) echo "+";
        echo (money_format('%i',$jBenefTotal))."&nbsp;&euro;</span></td>\n";

        // ROI
        echo "      <td>ROI</td>\n";
        echo "      <td>";
        echo "<span style=\"color:".valColor($roi)."\">";
        if($roi>0) echo "+";
        echo $roi."&nbsp;%</span>";
        echo "&nbsp;<a href=\"#\" class=\"infobulle\">&#128172;<span>Le&nbsp;ROI&nbsp;est&nbsp;";
        switch($roi){
            case($roi<0):
                echo "perdant";
                break;
            case($roi==0):
                echo "neutre";
                break;
            case($roi>0&&$roi<15):
                echo "gagnant";
                break;
            case($roi>=15):
                echo "excellent";
                break;
        }
        echo "&nbsp;!</span></a></td>\n";
        echo " </tr>\n";
        
        echo "  <tr>\n";
        // Succès
        echo "      <td>Succ&egrave;s</td>\n";
        echo "      <td>".$jSuccesTotal."</td>\n";
        
        // Gains
        echo "      <td>Gains</td>\n";
        echo "      <td>".money_format('%i',$jGainsTotal)."&nbsp;&euro;</td>\n";
        
        // Gains par mise
        echo "      <td>Gains&nbsp;par&nbsp;mise</td>\n";
        echo "      <td>".$gainParMise."</td>\n";
    

        echo " </tr>\n";

        echo " </tr>\n";
        
        // Taux de réussite
        echo "      <td>Taux&nbsp;de&nbsp;réussite</td>\n";
        echo "      <td>";
        if($jMisesTotal==0) $tauxReussite= 0;
        echo $tauxReussite."&nbsp;%</td>\n";

        // Cote moyenne jouée
        $coteMoy=(round($jCoteJoueeTotal/$jMisesTotal,2));
        echo "      <td>Cote&nbsp;moyenne&nbsp;jouée</td>\n";
        echo "      <td>".$coteMoy;
        if(($coteMoy<1.8)||($coteMoy>2.3)){
            echo "&nbsp;<a href=\"#\" class=\"infobulle\">&#128172;<span>Jeu&nbsp;";
            switch($coteMoy){
                case($coteMoy<1.5):
                    echo "trop prudent";
                    break;
                case($coteMoy<1.8):
                    echo "prudent";
                    break;
                case($coteMoy>3):
                    echo "trop spéculateur";
                    break;
                case($coteMoy>2.3):
                    echo "spéculateur";
                    break;
            }
            echo "&nbsp;!</span></a>";
        }
        echo "</td>\n";

        echo "      <td></td>\n";
        echo "      <td></td>\n";
        echo " </tr>\n";
        echo "</table>\n</p>";

        echo "<h3>&Eacute;volution du bénéfice</h3>\n";
?>


<?php
$largeur=400;
$hauteur=300;
$maxX=array_key_last($graph);
$maxY=end($graph);;
?>
<svg width="<?php echo $largeur;?>" height="<?php echo $hauteur;?>">
<!-- fond -->
<rect width="100%" height="100%" fill="#dec" stroke="#9c7" stroke-width="4"/>
        
<!-- margin -->
  <g class="layer" transform="translate(40,<?php echo ($hauteur/2);?>)">
  
<?php
foreach ($graph as $k => $v) {
    $cx=$k*10;
    $cy=-$v*2;
    $color=valColor(-($cy));
    echo "<circle r=\"2\" cx=\"".$cx."\" cy=\"".$cy."\" fill=\"".$color."\" />\n";
    echo "<line x1=\"".$cxPrec."\" y1=\"".$cyPrec."\" x2=\"".$cx."\" y2=\"".$cy."\" stroke=\"".$color."\" stroke-width=\"1\" />\n";
    $cxPrec=$cx;
    $cyPrec=$cy;
}


?>
<!-- Axe y -->
    <g class="y axis" fill="purple">
      <line x1="<?php echo -($largeur-10);?>" y1="0" x2="<?php echo ($largeur-10);?>" y2="0" stroke="#555" stroke-width="1" />
<?php
// Mesures de l'axe Y
for($i=-($hauteur/(2*25));$i<($hauteur/(2*25)+1);$i++){
    if($i!=0) {
        echo "<text text-anchor=\"end\" x=\"-6\" y=\"".(($i*20)+4)."\" fill=\"#583\">".-($i*10)."</text>\n";
        echo "<line x1=\"-2\" y1=\"".($i*20)."\" x2=\"2\" y2=\"".($i*20)."\" stroke=\"#583\" stroke-width=\"2\" />\n";
    }
}
?>

<!-- Axe x -->
    </g>
    <g class="x axis" fill="purple">
      <line x1="0" y1="<?php echo -($hauteur-10);?>" x2="0" y2="<?php echo ($hauteur-10);?>" stroke="#555" stroke-width="1" />
      <text x="5" y="20" fill="black">J1</text>
      <text x="<?php echo ($maxX*10)+5;?>" y="<?php echo (-($maxY*2)+15);?>" fill="black">J<?php echo $maxX;?></text>
    </g>

  </g>
</svg>
    

    

    
<?php
        echo "<h3>Bénéfice par journée</h3>\n";
        echo $table;      
?>
    </section>
    
