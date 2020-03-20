<?php

        echo "  		<option value=\"0\">...</option>\n";
        // On affiche chaque entrée
        while ($donnees = $reponse->fetch())
        {
            echo "  		<option value=\"".$donnees['id_club']."\">".$donnees['nom']."</option>\n";
        }
?>
