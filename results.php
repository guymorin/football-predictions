<?php

include("inc_changeJ.php");

// NAVIGATION MATCHS RESULTAT
include("journees_nav.php");
?>

    <section>
<?php


$modifie=0;
if(isset($_POST['modifie'])) $modifie=$_POST['modifie'];
// MODIFIER
if($modifie==1){
    // S'il y a une modification
    
if(isset($_POST['id_match'])) $idMatch=$_POST['id_match'];
if(isset($_POST['resultat'])) $rMatch=$_POST['resultat'];
if(isset($_POST['date'])) $dMatch=$_POST['date'];
if(isset($_POST['cote1'])) $c1Match=$_POST['cote1'];
if(isset($_POST['coteN'])) $cNMatch=$_POST['coteN'];
if(isset($_POST['cote2'])) $c2Match=$_POST['cote2'];
if(isset($_POST['rouge1'])) $r1Match=$_POST['rouge1'];
if(isset($_POST['rouge2'])) $r2Match=$_POST['rouge2'];
if(isset($_POST['blesse1'])) $b1Match=$_POST['blesse1'];
if(isset($_POST['blesse2'])) $b2Match=$_POST['blesse2'];

        $cpt=0;
        $req="";
        
        foreach($idMatch as $k){
            $req.="UPDATE matchs SET ";
            $req.="resultat='".$rMatch[$k]."'";
            $cpt=1;
            
            if($dMatch[$k]!=""){// Si resultat
                if($cpt==1){
                    $req.=",";
                    $cpt=0;
                }
                $req.="date='".$dMatch[$k]."'";
                $cpt=1;
            }
            if($c1Match[$k]>0){// Si resultat
                if($cpt==1){
                    $req.=",";
                    $cpt=0;
                }
                $req.="cote1='".$c1Match[$k]."'";
                $cpt=1;
            }
            if($cNMatch[$k]>0){// Si resultat
                if($cpt==1){
                    $req.=",";
                    $cpt=0;
                }
                $req.="coteN='".$cNMatch[$k]."'";
                $cpt=1;
            }
            if($c2Match[$k]>0){// Si resultat
                if($cpt==1){
                    $req.=",";
                    $cpt=0;
                }
                $req.="cote2='".$c2Match[$k]."'";
                $cpt=1;
            }
            if($r1Match[$k]>=1){// Si resultat
                if($cpt==1){
                    $req.=",";
                    $cpt=0;
                }
                $req.="rouge1='".$r1Match[$k]."'";
                $cpt=1;
            }
            if($r2Match[$k]>=1){// Si resultat
                if($cpt==1){
                    $req.=",";
                    $cpt=0;
                }
                $req.="rouge2='".$r2Match[$k]."'";
                $cpt=1;
            }
            if($b1Match[$k]>=1){// Si resultat
                if($cpt==1){
                    $req.=",";
                    $cpt=0;
                }
                $req.="blesse1='".$b1Match[$k]."'";
                $cpt=1;
            }
            if($b2Match[$k]>=1){// Si resultat
                if($cpt==1){
                    $req.=",";
                    $cpt=0;
                }
                $req.="blesse2='".$b2Match[$k]."'";
                $cpt=1;
            }
            $req.=" WHERE id_match='".$k."';";
        }
        
        $bdd->exec($req);
        popup("Modification des résultats pour J".$_SESSION['numJournee'].".","index.php?page=resultats");

    } else {
    
    include("resultats_matchs.php");
    
}
?>
    </section>
