<?php

        echo "  		<option value='0'>...</option>\n";
        while ($data = $response->fetch(PDO::FETCH_OBJ))
        {
            echo "  		<option value='".$data->id_team."'>".$data->name."</option>\n";
        }
?>
