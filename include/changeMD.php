<?php
/* Include to display the matchday */

    if(isset($_POST['matchdaySelect'])){
        // Values
        $v=explode(",",$_POST['matchdaySelect']);
        $_SESSION['matchdayId']=$v[0];
        $_SESSION['matchdayNum']=$v[1];
    }
?>