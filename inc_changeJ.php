<?php

// CHOIX DE LA JOURNEE

    if(isset($_POST['choixJournee'])){

        $v=explode(",",$_POST['choixJournee']);
        $_SESSION['idJournee']=$v[0];
        $_SESSION['numJournee']=$v[1];
    
    }
?>
