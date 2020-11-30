<?php
// Predictions matchgame default include file

use FootballPredictions\Language;
use FootballPredictions\Theme;
use FootballPredictions\Predictions;

echo "<h3>" . (Language::title('prediction')) . "</h3>\n";

// Select data
$data = result('selectCriterion',$pdo);
$counter = $pdo->rowCount();
if($counter > 0){
    
    if($_SESSION['role']==2) echo Predictions::switchButton($form, 'toManual');

    // Modify form
    echo "<form id='criterion' action='index.php?page=prediction' method='POST' onsubmit='return confirm();'>\n";
    echo $form->inputAction('modify');
    
    Predictions::teamsBonusMalus($pdo);
    
    // Predictions for the matchday
    foreach ($data as $d)
    {
        $pred = new Predictions();
        $isResult = false;
        if($d->result!="") $isResult=true;
        $pred->setCriteria($d, $pdo, $isResult);

        $pred->sumCriterion($d);
        $pred->displayCriteria($d, $form);
    }
    echo $form->submit(Theme::icon('modify')." ".Language::title('modify'));
    echo "</form>\n";
    
} else echo Language::title('noMatch');
?>  
