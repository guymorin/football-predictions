<?php

        echo "  		<option value='0'>...</option>\n";
        // On affiche chaque entrée
        while ($data = $response->fetch(PDO::FETCH_OBJ))
        {
            echo "  		<option value='".$data->id_team."'>".$data->name."</option>\n";
        }
?>
